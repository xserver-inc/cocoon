# Cocoon 開発用 Docker 環境

Cocoon テーマの開発・動作確認用の Docker 環境である。単一の `docker-compose.yml` を
`env/` 配下の env ファイルでパラメータ化し、複数の WordPress / PHP 組み合わせを同じ定義で起動する。

## 環境組み合わせ

| #   | WordPress | PHP | env ファイル                   | WordPress URL         | phpMyAdmin            | MySQL | 用途           |
| --- | --------- | --- | ------------------------------ | --------------------- | --------------------- | ----- | -------------- |
| 1   | 6.5       | 8.2 | `env/wp6.5-php8.2.env`         | http://localhost:8080 | http://localhost:8180 | :3080 |                |
| 2   | 6.6       | 8.2 | `env/wp6.6-php8.2.env`         | http://localhost:8081 | http://localhost:8181 | :3081 |                |
| 3   | 6.6       | 8.3 | `env/wp6.6-php8.3.env`         | http://localhost:8082 | http://localhost:8182 | :3082 |                |
| 4   | 6.7       | 8.2 | `env/wp6.7-php8.2.env`         | http://localhost:8083 | http://localhost:8183 | :3083 |                |
| 5   | 6.7       | 8.3 | `env/wp6.7-php8.3.env`         | http://localhost:8084 | http://localhost:8184 | :3084 |                |
| 6   | 6.8       | 8.3 | `env/wp6.8-php8.3.env`         | http://localhost:8085 | http://localhost:8185 | :3085 | 安定版（既定） |
| 7   | 最新      | 8.3 | `env/wp6.8-latest-php8.3.env`  | http://localhost:8086 | http://localhost:8186 | :3086 | 自動更新版     |
| 8   | 7.0       | 8.4 | `env/wp7.0-php8.4.env`         | http://localhost:8090 | http://localhost:8190 | :3090 |                |

ポートが重複しないため、複数の組み合わせを同時に起動できる。

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

### 特定の組み合わせで起動

env ファイルを `--env-file` で指定する。

```bash
docker compose --env-file env/wp7.0-php8.4.env up -d
```

各 env ファイルは `COMPOSE_PROJECT_NAME` が異なるため、コンテナ・ネットワーク・ボリュームは
組み合わせごとに自動的に分離される。

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
├── .env                 # 既定の起動設定（安定版）と共通の資格情報
├── .env.example         # .env のテンプレート
├── env/                 # 組み合わせごとの env ファイル（画像タグ・ポート・プロジェクト名）
│   ├── wp6.5-php8.2.env
│   ├── …
│   └── wp7.0-php8.4.env
├── mysql/
│   └── init/            # MySQL 初期化 SQL（初回起動時のみ実行）
└── README.md
```

## 環境変数

`.env`（または `env/` の各ファイル）で以下を上書きできる。未指定の場合は `docker-compose.yml`
の既定値（安定版・下記の値）が使われる。

| 変数                     | 既定値                        | 説明                                    |
| ------------------------ | ----------------------------- | --------------------------------------- |
| `COMPOSE_PROJECT_NAME`   | `cocoon-wp68-php83`           | コンテナ/ネットワーク/ボリュームの接頭辞 |
| `WP_IMAGE`               | `wordpress:6.8-php8.3-apache` | WordPress イメージタグ                   |
| `WEB_PORT`               | `8085`                        | WordPress の公開ポート                   |
| `DB_PORT`                | `3085`                        | MySQL の公開ポート                       |
| `PMA_PORT`               | `8185`                        | phpMyAdmin の公開ポート                  |
| `WORDPRESS_DB_NAME`      | `wordpress`                   | データベース名                          |
| `WORDPRESS_DB_USER`      | `wordpress`                   | データベースユーザー                    |
| `WORDPRESS_DB_PASSWORD`  | `wordpress`                   | データベースパスワード                  |
| `WORDPRESS_TABLE_PREFIX` | `wp_`                         | テーブル接頭辞                          |
| `MYSQL_ROOT_PASSWORD`    | `rootpassword`                | MySQL root パスワード                   |
| `WORDPRESS_DEBUG`        | `1`                           | WordPress デバッグモード                |

## テーマの有効化

1. WordPress 管理画面（各 URL）にアクセスし、初期設定を行う。
2. 「外観」→「テーマ」で「Cocoon」を有効化する。

テーマはリポジトリのルートを `wp-content/themes/cocoon` にマウントしているため、ローカルの
編集がそのまま反映される。

## 統合テスト用データベース

MySQL コンテナは初回起動時に `mysql/init/` の SQL を実行し、本番用 DB に加えて
統合テスト用の `wordpress_test` データベースを作成する。WordPress テストスイートは指定した
DB の全テーブルを破棄するため、開発用 DB と分離している。テストの実行方法はリポジトリの
テスト関連ドキュメントを参照すること。
