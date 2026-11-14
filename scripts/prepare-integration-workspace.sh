#!/usr/bin/env sh
# 統合テスト用ソースの同期とPOSIXシェル向けのLF改行の維持
set -eu

workspace_source=$(realpath -m -- "${1:?同期元の指定が必要です}")
workspace_destination=$(realpath -m -- "${2:?同期先の指定が必要です}")
workspace_marker=.cocoon-integration-workspace
workspace_signature='Cocoon integration workspace v1'

if [ ! -d "$workspace_source" ]; then
  printf '%s\n' '同期元のディレクトリがありません。' >&2
  exit 1
fi

# 元ファイルや親ディレクトリの削除を避けるための、重複した保存先の拒否
case "$workspace_source/" in
  "$workspace_destination/"*) printf '%s\n' '同期元と同期先の包含関係は使用できません。' >&2; exit 1 ;;
esac
case "$workspace_destination/" in
  "$workspace_source/"*) printf '%s\n' '同期元と同期先の包含関係は使用できません。' >&2; exit 1 ;;
esac

mkdir -p "$workspace_destination"
if [ -f "$workspace_destination/$workspace_marker" ]; then
  if [ "$(cat "$workspace_destination/$workspace_marker")" != "$workspace_signature" ]; then
    printf '%s\n' '同期先の識別情報が一致しません。' >&2
    exit 1
  fi
else
  # Composer用マウント以外が空の場合だけを対象とした、専用領域の初期化
  if [ -n "$(find "$workspace_destination" -mindepth 1 -maxdepth 1 ! -name vendor -print -quit)" ]; then
    printf '%s\n' '専用領域ではない既存ディレクトリへの同期はできません。' >&2
    exit 1
  fi
  printf '%s\n' "$workspace_signature" > "$workspace_destination/$workspace_marker"
fi

printf '%s\n' 'Preparing the integration workspace...'
# Windows側でまとめたアーカイブによる、共有フォルダーのファイル単位の読み込みの削減
workspace_staging=''
cleanup() {
  case "$workspace_staging" in
    /tmp/cocoon-integration-source.*) rm -rf -- "$workspace_staging" ;;
  esac
}
trap cleanup EXIT
if [ -n "${3:-}" ]; then
  workspace_staging=$(mktemp -d /tmp/cocoon-integration-source.XXXXXX)
  # 共有フォルダーへの小さな読み込み要求をまとめた、1MiB単位でのアーカイブの展開
  tar --extract --blocking-factor=2048 --warning=no-unknown-keyword --file "$3" --directory "$workspace_staging"
  workspace_source="$workspace_staging"
fi

# 開発用データと依存関係のコピーを避け、削除済みのテストも持ち越さない実行用ソースの同期
rsync --archive --checksum --delete \
  --exclude="/$workspace_marker" \
  --exclude='/.git/' \
  --exclude='/.agents/' \
  --exclude='/.agent/' \
  --exclude='/.codex/' \
  --exclude='/.aws/' \
  --exclude='/.cursor/' \
  --exclude='/.vscode/' \
  --exclude='/.github/docs/' \
  --exclude='/.phpunit.cache/' \
  --exclude='/.phpunit.result.cache' \
  --exclude='/vendor/' \
  --exclude='/scratch/' \
  --exclude='/docker/' \
  --exclude='/test-results/' \
  --exclude='node_modules/' \
  "$workspace_source/" "$workspace_destination/"
printf '%s\n' 'Integration workspace ready.'
