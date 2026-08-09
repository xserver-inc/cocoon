#!/usr/bin/env bash
set -Eeuo pipefail

if [[ ! "${MYSQL_USER:-}" =~ ^[a-zA-Z0-9_]+$ ]]; then
    echo >&2 'MYSQL_USERには英数字とアンダースコアのみ使用できます。'
	exit 1
fi

MYSQL_PWD="${MYSQL_ROOT_PASSWORD}" mysql --protocol=socket -uroot <<-EOSQL
	CREATE DATABASE IF NOT EXISTS \`wordpress_test\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
	GRANT ALL PRIVILEGES ON \`wordpress_test\`.* TO '${MYSQL_USER}'@'%';
	FLUSH PRIVILEGES;
EOSQL
