#!/usr/bin/env sh
# POSIXシェル向けのLF改行の維持
set -eu

repository_root=$(CDPATH= cd -- "$(dirname -- "$0")/../.." && pwd)
workspace_preparer="$repository_root/scripts/prepare-integration-workspace.sh"
test_root=$(mktemp -d /tmp/cocoon-workspace-test.XXXXXX)

cleanup() {
  # 今回作成した一時ディレクトリだけを対象とした後片付け
  case "$test_root" in
    /tmp/cocoon-workspace-test.*) rm -rf -- "$test_root" ;;
  esac
}
trap cleanup EXIT

source_dir="$test_root/source"
destination_dir="$test_root/destination"
mkdir -p "$source_dir/tests/Integration" "$source_dir/scratch" "$source_dir/blocks/node_modules" "$source_dir/vendor" "$destination_dir/vendor"
printf '%s\n' '<?php echo "同期元";' > "$source_dir/functions.php"
printf '%s\n' '削除対象のテスト' > "$source_dir/tests/Integration/RemovedTest.php"
printf '%s\n' '隠しファイル' > "$source_dir/.htaccess"
printf '%s\n' '除外対象' > "$source_dir/scratch/temporary.txt"
printf '%s\n' '除外対象' > "$source_dir/blocks/node_modules/dependency.js"
printf '%s\n' '除外対象' > "$source_dir/vendor/source.txt"
printf '%s\n' '保持対象' > "$destination_dir/vendor/installed.txt"

# ソースと隠しファイルのコピー、および依存関係・一時データの除外の検証
/bin/sh "$workspace_preparer" "$source_dir" "$destination_dir"
cmp "$source_dir/functions.php" "$destination_dir/functions.php"
cmp "$source_dir/.htaccess" "$destination_dir/.htaccess"
test -f "$destination_dir/vendor/installed.txt"
test ! -e "$destination_dir/vendor/source.txt"
test ! -e "$destination_dir/scratch"
test ! -e "$destination_dir/blocks/node_modules"
printf '%s\n' '成功: ソースのコピーと依存関係・一時データの除外'

# 変更・追加・削除を含む再同期と、Composer依存関係の保持の検証
printf '%s\n' '<?php echo "変更後";' > "$source_dir/functions.php"
# サイズと更新日時が同じ場合も対象とした、内容の変更検出の検証
touch -r "$destination_dir/functions.php" "$source_dir/functions.php"
printf '%s\n' '追加テスト' > "$source_dir/tests/Integration/AddedTest.php"
rm -- "$source_dir/tests/Integration/RemovedTest.php"
/bin/sh "$workspace_preparer" "$source_dir" "$destination_dir"
cmp "$source_dir/functions.php" "$destination_dir/functions.php"
test -f "$destination_dir/tests/Integration/AddedTest.php"
test ! -e "$destination_dir/tests/Integration/RemovedTest.php"
test -f "$destination_dir/vendor/installed.txt"
printf '%s\n' '成功: 更新・追加・削除の反映と依存関係の保持'

# 既存ファイルのある未識別ディレクトリへの同期拒否の検証
unowned_dir="$test_root/unowned"
mkdir -p "$unowned_dir"
printf '%s\n' '元のファイル' > "$unowned_dir/existing.txt"
if /bin/sh "$workspace_preparer" "$source_dir" "$unowned_dir" > "$test_root/rejected.log" 2>&1; then
  printf '%s\n' '失敗: 既存ディレクトリへの同期を拒否できませんでした。' >&2
  exit 1
fi
test "$(cat "$unowned_dir/existing.txt")" = '元のファイル'
test ! -e "$unowned_dir/.cocoon-integration-workspace"
printf '%s\n' '成功: 既存ディレクトリの保護'

# 同一ディレクトリ・親子ディレクトリ・未存在の同期元の拒否の検証
for rejected_destination in "$source_dir" "$source_dir/nested" "$test_root"; do
  if /bin/sh "$workspace_preparer" "$source_dir" "$rejected_destination" > "$test_root/rejected.log" 2>&1; then
    printf '%s\n' '失敗: 包含関係のあるディレクトリへの同期を拒否できませんでした。' >&2
    exit 1
  fi
done
test ! -e "$source_dir/nested"
if /bin/sh "$workspace_preparer" "$test_root/missing" "$test_root/new" > "$test_root/rejected.log" 2>&1; then
  printf '%s\n' '失敗: 未存在の同期元を拒否できませんでした。' >&2
  exit 1
fi
test ! -e "$test_root/new"
printf '%s\n' '成功: 包含関係と未存在の同期元の拒否'

# アーカイブ経由でも対象を削除・更新する、実際の展開と再同期の検証
archive_source="$test_root/archive-source"
mkdir -p "$archive_source"
printf '%s\n' 'アーカイブの内容' > "$archive_source/functions.php"
tar --create --file "$test_root/source.tar" --directory "$archive_source" .
/bin/sh "$workspace_preparer" "$source_dir" "$destination_dir" "$test_root/source.tar"
cmp "$archive_source/functions.php" "$destination_dir/functions.php"
test ! -e "$destination_dir/tests/Integration/AddedTest.php"
test -f "$destination_dir/vendor/installed.txt"
printf '%s\n' '成功: アーカイブの展開・同期と古いファイルの削除'

# 壊れたアーカイブを正常扱いしない、展開失敗時の既存コピーの保持の検証
printf '%s\n' '壊れたアーカイブ' > "$test_root/invalid.tar"
if /bin/sh "$workspace_preparer" "$source_dir" "$destination_dir" "$test_root/invalid.tar" > "$test_root/rejected.log" 2>&1; then
  printf '%s\n' '失敗: 壊れたアーカイブを拒否できませんでした。' >&2
  exit 1
fi
cmp "$archive_source/functions.php" "$destination_dir/functions.php"
printf '%s\n' '成功: アーカイブの展開失敗時の保護'

# 識別情報が変更された保存先への同期拒否と、元ファイルの保持の検証
printf '%s\n' '異なる識別情報' > "$destination_dir/.cocoon-integration-workspace"
printf '%s\n' '同期禁止' > "$source_dir/functions.php"
if /bin/sh "$workspace_preparer" "$source_dir" "$destination_dir" > "$test_root/rejected.log" 2>&1; then
  printf '%s\n' '失敗: 識別情報の不一致を拒否できませんでした。' >&2
  exit 1
fi
test "$(cat "$destination_dir/functions.php")" = 'アーカイブの内容'
printf '%s\n' '成功: 識別情報の不一致時の保護'
