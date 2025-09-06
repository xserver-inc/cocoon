# Cocoon WordPress 開発環境

このディレクトリには、Cocoon WordPressテーマの開発用Dockerコンテナ環境が含まれています。

## 環境組み合わせ

| # | WordPress | PHP | WordPress URL | MySQL | phpMyAdmin | 用途 |
|---|-----------|-----|---------------|-------|------------|------|
| 1 | 6.5 | 8.2 | http://localhost:8080 | :3080 | http://localhost:8180 |  |
| 2 | 6.6 | 8.2 | http://localhost:8081 | :3081 | http://localhost:8181 |  |
| 3 | 6.6 | 8.3 | http://localhost:8082 | :3082 | http://localhost:8182 |  |
| 4 | 6.7 | 8.2 | http://localhost:8083 | :3083 | http://localhost:8183 |  |
| 5 | 6.7 | 8.3 | http://localhost:8084 | :3084 | http://localhost:8184 |  |
| 6 | 6.8 | 8.3 | http://localhost:8085 | :3085 | http://localhost:8185 | 最新安定版 |
| 7 | 6.8-latest | 8.3-latest | http://localhost:8086 | :3086 | http://localhost:8186 | 自動更新版 |

- **除外**: PHP 8.1（2025年12月31日EOL）、WordPress 6.4以下

## 🛠 動作環境

### 検証済み環境
- **OS**: Ubuntu 24.04.2 LTS (Kernel 6.8.0-53-generic)
- **Docker**: 27.5.1
- **Docker Compose**: 1.29.2

### 必要な環境
- Docker 20.10.0 以上
- Docker Compose 1.29.0 以上
- 利用可能メモリ: 4GB以上推奨
- ディスク容量: 10GB以上推奨

## 🚀 クイックスタート

### 初回セットアップ

Docker環境をセットアップしていない場合は、以下の手順を実行してください：

```bash
# Dockerがインストールされていない場合
sudo apt update
sudo apt install docker.io docker-compose

# Dockerサービスを開始
sudo systemctl start docker
sudo systemctl enable docker

# 現在のユーザーをdockerグループに追加（権限エラー回避）
sudo usermod -aG docker $USER

# 新しいグループ権限を適用
newgrp docker
# または一度ログアウトして再ログイン
```

### 基本的な使用方法（最新版）

```bash
cd docker
docker-compose up -d
```

デフォルトでは WordPress 6.8 + PHP 8.3 の環境が起動します（http://localhost:8085）。

### 特定のバージョンで起動

```bash
# 1. WordPress 6.5 + PHP 8.2
docker-compose -f docker-compose.wp6.5-php8.2.yml up -d

# 2. WordPress 6.6 + PHP 8.2
docker-compose -f docker-compose.wp6.6-php8.2.yml up -d

# 3. WordPress 6.6 + PHP 8.3
docker-compose -f docker-compose.wp6.6-php8.3.yml up -d

# 4. WordPress 6.7 + PHP 8.2
docker-compose -f docker-compose.wp6.7-php8.2.yml up -d

# 5. WordPress 6.7 + PHP 8.3
docker-compose -f docker-compose.wp6.7-php8.3.yml up -d

# 6. WordPress 6.8 + PHP 8.3（最新安定版）
docker-compose -f docker-compose.wp6.8-php8.3.yml up -d

# 7. WordPress 6.8-latest + PHP 8.3（自動更新版）
docker-compose -f docker-compose.wp6.8-latest-php8.3.yml up -d
```

### 環境の停止

```bash
# デフォルト環境を停止
docker-compose down

# 特定の環境を停止 (データは保持)
docker-compose -f docker-compose.wp6.8-php8.3.yml down

# データも含めて完全に削除
docker-compose -f docker-compose.wp6.8-php8.3.yml down -v
```

## 📁 ファイル構成

```
docker/
├── docker-compose.yml                         # デフォルト環境（最新安定版）へのシンボリックリンク
├── docker-compose.wp6.5-php8.2.yml           # 1. WordPress 6.5 + PHP 8.2
├── docker-compose.wp6.6-php8.2.yml           # 2. WordPress 6.6 + PHP 8.2
├── docker-compose.wp6.6-php8.3.yml           # 3. WordPress 6.6 + PHP 8.3
├── docker-compose.wp6.7-php8.2.yml           # 4. WordPress 6.7 + PHP 8.2
├── docker-compose.wp6.7-php8.3.yml           # 5. WordPress 6.7 + PHP 8.3
├── docker-compose.wp6.8-php8.3.yml           # 6. WordPress 6.8 + PHP 8.3（最新安定版）
├── docker-compose.wp6.8-latest-php8.3.yml    # 7. WordPress 6.8-latest + PHP 8.3（自動更新版）
├── .env                                       # 環境変数設定
├── .env.example                               # 環境変数テンプレート
├── mysql/
│   └── init/                                 # MySQLの初期化スクリプト
└── README.md                                 # このファイル
```

## ⚙️ 環境変数設定

`.env` ファイルで以下の設定をカスタマイズできます：

```bash
# データベース設定
WORDPRESS_DB_NAME=wordpress
WORDPRESS_DB_USER=wordpress
WORDPRESS_DB_PASSWORD=wordpress
WORDPRESS_TABLE_PREFIX=wp_

# MySQL Root パスワード
MYSQL_ROOT_PASSWORD=rootpassword

# WordPress デバッグモード
WORDPRESS_DEBUG=true

# WordPress サイト設定
WORDPRESS_SITE_URL=http://localhost:8080
WORDPRESS_SITE_TITLE=Cocoon Development Site
WORDPRESS_ADMIN_USER=admin
WORDPRESS_ADMIN_PASSWORD=admin123
WORDPRESS_ADMIN_EMAIL=admin@example.com
```

## 🔧 詳細な使用方法

### WordPress初期設定

1. ブラウザで対応するWordPress URLにアクセス
2. 言語を選択（日本語）
3. サイト情報を入力：
   - サイト名: Cocoon Development Site
   - ユーザー名: admin
   - パスワード: admin123
   - メールアドレス: admin@example.com

### Cocoonテーマの有効化

1. WordPress管理画面にログイン
2. 「外観」→「テーマ」へ移動
3. 「Cocoon」テーマを有効化


### データのリセット

開発データを完全にリセットしたい場合：

```bash
# 特定の環境のデータをリセット
docker-compose -f docker-compose.wp6.8-php8.3.yml down -v
docker-compose -f docker-compose.wp6.8-php8.3.yml up -d

# 全てのDockerリソースをクリーンアップ
docker system prune -a
docker volume prune
```

### 複数環境の同時起動

異なるポートを使用しているため、複数のWordPress環境を同時に起動できます：

```bash
# 最新版と安定版を同時起動
docker-compose -f docker-compose.wp6.8-php8.3.yml up -d        # :8085
docker-compose -f docker-compose.wp6.7-php8.2.yml up -d        # :8083
docker-compose -f docker-compose.wp6.5-php8.2.yml up -d        # :8080

# 全ての環境を停止
docker-compose -f docker-compose.wp6.8-php8.3.yml down
docker-compose -f docker-compose.wp6.7-php8.2.yml down
docker-compose -f docker-compose.wp6.5-php8.2.yml down
```

### 推奨使用方法

```bash
# 通常の開発: 最新安定版を使用
docker-compose -f docker-compose.wp6.8-php8.3.yml up -d

# 互換性テスト: 複数バージョンでテスト
docker-compose -f docker-compose.wp6.7-php8.2.yml up -d
docker-compose -f docker-compose.wp6.6-php8.2.yml up -d
docker-compose -f docker-compose.wp6.5-php8.2.yml up -d

# 最新機能追従: 自動更新版を使用
docker-compose -f docker-compose.wp6.8-latest-php8.3.yml up -d
```
