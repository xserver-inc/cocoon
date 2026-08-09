# Cocoon 開発用 Docker 環境

Cocoon テーマの開発・動作確認用の Docker 環境である。単一の `docker-compose.yml` を
`env/` 配下の env ファイルでパラメータ化し、複数の WordPress / PHP 組み合わせを同じ定義で起動する。

## 環境組み合わせ

| #   | WordPress | PHP | env ファイル                   | WordPress URL         | phpMyAdmin（任意起動） | MySQL             | 用途           |
| --- | --------- | --- | ------------------------------ | --------------------- | --------------------- | ----------------- | -------------- |
| 1   | 6.5       | 8.2 | `env/wp6.5-php8.2.env`         | http://localhost:8080 | http://localhost:8180 | localhost:3080    |                |
| 2   | 6.6       | 8.2 | `env/wp6.6-php8.2.env`         | http://localhost:8081 | http://localhost:8181 | localhost:3081    |                |
| 3   | 6.6       | 8.3 | `env/wp6.6-php8.3.env`         | http://localhost:8082 | http://localhost:8182 | localhost:3082    |                |
| 4   | 6.7       | 8.2 | `env/wp6.7-php8.2.env`         | http://localhost:8083 | http://localhost:8183 | localhost:3083    |                |
| 5   | 6.7       | 8.3 | `env/wp6.7-php8.3.env`         | http://localhost:8084 | http://localhost:8184 | localhost:3084    |                |
| 6   | 6.8       | 8.3 | `env/wp6.8-php8.3.env`         | http://localhost:8085 | http://localhost:8185 | localhost:3085    | 安定版（既定） |
| 7   | 最新      | 8.3 | `env/wp6.8-latest-php8.3.env`  | http://localhost:8086 | http://localhost:8186 | localhost:3086    | 自動更新版     |
| 8   | 7.0       | 8.4 | `env/wp7.0-php8.4.env`         | http://localhost:8090 | http://localhost:8190 | localhost:3090    |                |

ポートは既定で `127.0.0.1` のみに公開される。ポートが重複しないため、複数の組み合わせを同時に起動できる。

## 必要な環境

- Docker Engine 20.10.0 以上（Compose v2 プラグイン同梱）
- 利用可能メモリ 4GB 以上、ディスク 10GB 以上を推奨

> Compose v2 を前提とする（`docker compose` サブコマンド）。旧 `docker-compose` v1 では
> `--env-file` の解釈が異なるため、v2 を使用すること。

## 使い方

### 既定（安定版）で起動

```bash
cd docker
docker compose up -d
```

WordPress 6.8 + PHP 8.3 が起動する（http://localhost:8085）。既定値は `docker-compose.yml` と
`.env` に定義してあるため、env ファイルの指定は不要である。

phpMyAdminは通常起動から除外している。必要なときだけ次のコマンドで起動する。

```bash
docker compose --profile admin up -d phpmyadmin
```

### 特定の組み合わせで起動

env ファイルを `--env-file` で指定する。

```bash
docker compose --env-file env/wp7.0-php8.4.env up -d
```

各 env ファイルは `COMPOSE_PROJECT_NAME` が異なるため、コンテナ・ネットワーク・ボリュームは
組み合わせごとに自動的に分離される。

> **注意**: `--env-file` を指定すると `.env` は読み込まれない（置き換えられる）。
> `.env` で変更した資格情報などを `env/` の組み合わせにも適用したい場合は、
> `--env-file` を複数指定する（Compose v2.17 以上。後に指定した方が優先）。
>
> ```bash
> docker compose --env-file .env --env-file env/wp7.0-php8.4.env up -d
> ```

### 停止・削除

```bash
# 停止（データは保持）
docker compose --env-file env/wp7.0-php8.4.env down

# データ（DB・アップロード）も含めて完全に削除
docker compose --env-file env/wp7.0-php8.4.env down -v
```

env ファイル無しで起動したもの（既定）は `docker compose down` で停止する。

### 複数環境の同時起動

```bash
docker compose --env-file env/wp6.8-php8.3.env up -d   # :8085
docker compose --env-file env/wp6.7-php8.2.env up -d   # :8083
docker compose --env-file env/wp6.5-php8.2.env up -d   # :8080
```

## ファイル構成

```
docker/
├── docker-compose.yml   # パラメータ化した唯一の Compose 定義
├── dev.ps1              # 起動・テスト・監視をまとめた操作スクリプト
├── .env                 # 既定の起動設定（安定版）と共通の資格情報
├── .env.example         # .env のテンプレート
├── phpunit/             # PHPバージョンを指定できるテスト用イメージ
├── env/                 # 組み合わせごとの env ファイル（画像タグ・ポート・プロジェクト名）
│   ├── wp6.5-php8.2.env
│   ├── …
│   └── wp7.0-php8.4.env
├── mysql/
│   └── init/            # MySQL 初期化スクリプト（初回起動時のみ実行）
└── README.md
```

## 環境変数

`.env`（または `env/` の各ファイル）で以下を上書きできる。未指定の場合は `docker-compose.yml`
の既定値（安定版・下記の値）が使われる。

| 変数                     | 既定値                        | 説明                                    |
| ------------------------ | ----------------------------- | --------------------------------------- |
| `COMPOSE_PROJECT_NAME`   | `cocoon-wp68-php83`           | コンテナ/ネットワーク/ボリュームの接頭辞 |
| `WP_IMAGE`               | `wordpress:6.8-php8.3-apache` | WordPress イメージタグ                   |
| `PHP_TEST_VERSION`       | `8.3`                         | PHPUnitコンテナのPHPバージョン           |
| `BIND_HOST`              | `127.0.0.1`                   | ホスト側ポートのバインド先               |
| `WEB_PORT`               | `8085`                        | WordPress の公開ポート                   |
| `DB_PORT`                | `3085`                        | MySQL の公開ポート                       |
| `PMA_PORT`               | `8185`                        | phpMyAdmin の公開ポート                  |
| `WORDPRESS_DB_NAME`      | `wordpress`                   | データベース名                          |
| `WORDPRESS_DB_USER`      | `wordpress`                   | データベースユーザー                    |
| `WORDPRESS_DB_PASSWORD`  | `wordpress`                   | データベースパスワード                  |
| `WORDPRESS_TABLE_PREFIX` | `wp_`                         | テーブル接頭辞                          |
| `MYSQL_ROOT_PASSWORD`    | `rootpassword`                | MySQL root パスワード                   |
| `WORDPRESS_DEBUG`        | `1`                           | WordPress デバッグモード                |
| `WORDPRESS_SITE_TITLE`   | `Cocoon Dev`                  | 自動セットアップ時のサイト名            |
| `WORDPRESS_ADMIN_USER`   | `admin`                       | 管理者ユーザー名                        |
| `WORDPRESS_ADMIN_PASSWORD` | `admin123`                  | 管理者パスワード                        |
| `WORDPRESS_ADMIN_EMAIL`  | `admin@example.com`           | 管理者メールアドレス                    |
| `WORDPRESS_CHILD_THEME_NAME` | `Cocoon Child`            | 生成する子テーマの表示名                |

## ローカル実機テスト環境

管理画面やフロントエンドの表示・挙動を確認するときは、原則としてこのDocker環境を使用する。
AIによるブラウザー確認でも `localhost` へ到達できるため、外部テストサーバーへ反映する前に
ローカルで実装、テスト、修正を反復する。

### 既定のテスト環境情報

| 項目 | 内容 |
| --- | --- |
| WordPress | 6.8系（`wordpress:6.8-php8.3-apache`） |
| Web実行用PHP | 8.3系 |
| PHPUnit実行用PHP | 8.3系 |
| データベース | MySQL 8.0 |
| 親テーマ | `cocoon`（このリポジトリを直接マウント） |
| 有効テーマ | `cocoon-child`（親テーマ: `cocoon`） |
| WordPress | http://localhost:8085 |
| WordPress管理画面 | http://localhost:8085/wp-admin/ |
| phpMyAdmin | http://localhost:8185 |
| MySQL公開ポート | `3085` |
| 管理者ユーザー名 | `admin` |
| 管理者パスワード | `admin123` |

phpMyAdminは `pwsh -NoProfile -File docker/dev.ps1 pma` を実行したときだけ起動する。
上記の認証情報はローカル開発専用であり、公開環境では使用しない。`BIND_HOST` を変更すると
同一ネットワークなどから到達できる可能性があるため、通常は `127.0.0.1` のまま使用する。イメージを再取得した場合は
WordPressやPHPのパッチバージョンが更新されることがあるため、固定値ではなくイメージタグを基準とする。

### 検証済みの基準

2026年8月9日に既定環境で次の結果を確認済みである。

| 項目 | 検証結果 |
| --- | --- |
| Docker Engine | 29.6.2 |
| WordPressコンテナ | WordPress 6.8系、PHP 8.3.28 |
| PHPUnitコンテナ | PHPUnit 11.5.56、PHP 8.3.33 |
| PHPUnit結果 | 1274テスト、9792アサーション、全件成功 |
| コンテナ状態 | WordPressとMySQLのヘルスチェック成功 |
| テーマ状態 | `cocoon` 親テーマと `cocoon-child` 子テーマの読み込み成功 |
| 構文検証 | Compose設定、CocoonのPHP、変更監視スクリプトの構文検証成功 |
| ブラウザー検証 | フロントエンドの初期記事、サイドバー、フッターの表示成功 |

これは環境構築時の基準値である。実装時は過去の成功結果だけで判断せず、変更後に一括テストと
ブラウザー確認を改めて実行する。

### ローカル実機テストの標準手順

PowerShell 7でプロジェクトルートから次の順に実行する。

```powershell
# コンテナ状態の確認
pwsh -NoProfile -File docker/dev.ps1 status

# 未起動時の環境起動
pwsh -NoProfile -File docker/dev.ps1 up

# DB確認が必要な場合だけphpMyAdminを起動
pwsh -NoProfile -File docker/dev.ps1 pma

# Compose構文、PHPUnit、Cocoon構文の一括確認
pwsh -NoProfile -File docker/dev.ps1 check
```

一括確認が成功したら、ブラウザーで次の項目を確認する。

- フロントエンド（http://localhost:8085）の対象表示と操作
- 管理画面（http://localhost:8085/wp-admin/）の対象設定と保存結果
- PC表示と、変更内容に応じて必要なレスポンシブ表示
- ブラウザーコンソールのJavaScriptエラー
- `cocoon` 親テーマと `cocoon-child` 子テーマの読み込み

実装中は `pwsh -NoProfile -File docker/dev.ps1 loop` を起動すると、ファイル保存後にPHPUnitを
再実行できる。表示確認では変更後にページを再読み込みする。

`docker compose down -v` はデータベースとアップロードを削除するため、環境の初期化を明示的に
求められた場合を除いて使用しない。

## 自動初期セットアップ

`up` すると `wp-cli` サービスが初回起動時に一度だけ実行され、以下を自動で行う。手動での
WordPress インストールやテーマ有効化は不要である。

1. `wp core install` による WordPress のインストール（サイト名・管理者は上記の環境変数から）
2. Cocoon 公式が推奨する親子構成に合わせ、子テーマ `cocoon-child`
   （親 = このリポジトリの `cocoon`）を `wp scaffold child-theme` で生成
3. 子テーマ `cocoon-child` を有効化

インストール済みの場合は再実行してもスキップされる（冪等）。既定の管理者は
`admin` / `admin123`（`/wp-admin` からログイン）。

親テーマ（このリポジトリ）はルートを `wp-content/themes/cocoon` にマウントしているため、
ローカルの編集がそのまま反映される。子テーマは WordPress データボリューム側に生成される。

## 反復開発コマンド

PowerShell 7から `docker/dev.ps1` を実行すると、Dockerの起動、テスト、状態確認を同じ操作で行える。
Docker Desktopがユーザー単位でインストールされていてPATHに含まれない場合も、標準的な保存場所から
`docker.exe` を自動検出する。

```powershell
# WordPressの起動とCocoon子テーマの有効化
pwsh -NoProfile -File docker/dev.ps1 up

# phpMyAdminの任意起動
pwsh -NoProfile -File docker/dev.ps1 pma

# Docker内でPHPUnitを実行
pwsh -NoProfile -File docker/dev.ps1 test

# Compose構文、PHPUnit、起動済みWordPressの一括確認
pwsh -NoProfile -File docker/dev.ps1 check

# コンテナ状態と直近ログの確認
pwsh -NoProfile -File docker/dev.ps1 status
pwsh -NoProfile -File docker/dev.ps1 logs

# データを保持したまま停止
pwsh -NoProfile -File docker/dev.ps1 down
```

別の組み合わせでは、第2引数にenvファイルを指定する。

```powershell
pwsh -NoProfile -File docker/dev.ps1 up env/wp7.0-php8.4.env
pwsh -NoProfile -File docker/dev.ps1 test env/wp7.0-php8.4.env
```

PHPUnitコンテナの既定はPHP 8.3である。`env/` 配下の各ファイルには `PHP_TEST_VERSION` も定義しているため、
envファイルを指定するとWordPress側と同じPHP 8.2、8.3、8.4へ自動的に切り替わる。
テスト用Composer依存関係は `composer.lock` で固定される。依存関係とキャッシュはDockerボリュームに
保存されるため、ホスト側の `vendor/` は変更されない。

## 変更監視ループ

次のコマンドはPHP、JavaScript、CSS、設定ファイルの保存を監視し、変更後にDocker内のPHPUnitを再実行する。
テスト実行中に別の変更が届いた場合は、現在のテスト終了後にもう一度実行する。

```powershell
pwsh -NoProfile -File docker/dev.ps1 loop
```

停止するときは `Ctrl+C` を押す。この監視ループにより、実装、テスト結果の確認、修正、再テストを
短いサイクルで繰り返せる。

## 統合テスト用データベース

MySQL コンテナは初回起動時に `mysql/init/` のスクリプトを実行し、開発用 DB に加えて
統合テスト用の `wordpress_test` データベースを作成する。WordPress テストスイートは指定した
DB の全テーブルを破棄するため、開発用 DB と分離している。`WORDPRESS_DB_USER` を変更した場合も、
英数字とアンダースコアで構成されたユーザー名であれば同じユーザーへ権限を付与する。テストの実行方法はリポジトリの
テスト関連ドキュメントを参照すること。

## 旧構成（環境別 Compose ファイル）からの移行

以前の `docker-compose.wp*.yml` 構成とはプロジェクト名・ボリューム名・DB 名
（旧: `wordpress_68_php83` など → 新: `wordpress`）が異なるため、旧環境のデータは
引き継がれず、初回起動時に新しい環境が作成される。

旧環境のコンテナ・ボリュームが不要な場合は、以下で削除できる。

```bash
# 旧環境のコンテナを確認して削除（コンテナ名: cocoon-wordpress-* / cocoon-mysql-* / cocoon-phpmyadmin-*）
docker ps -a --filter "name=cocoon-"
docker rm -f <コンテナ名>

# 未使用になった旧ボリューム・ネットワークを削除
docker volume prune
docker network prune
```
