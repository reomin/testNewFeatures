## Goss を使った環境検証の実践

このプロジェクトでは、E2E テスト前の環境検証に goss を使用した実装例を提供しています。

### プロジェクト構成

- **PHP 8.3** (Todo アプリ)
- **MySQL 8.0** (データベース)
- **Elasticsearch 8.9** (検索用)
- **Redis 7** (キャッシュ用)
- **React** (フロントエンド)

### 環境構築

```bash
# サービス起動
docker-compose up -d

# 全サービスが起動するまで少し待つ
sleep 60

# サービス状態確認
docker-compose ps
```

### Goss による環境検証

```bash
# Gossのインストール (macOS)
curl -fsSL https://github.com/goss-org/goss/releases/download/v0.4.9/goss-darwin-amd64 -o goss && chmod +x goss && sudo mv goss /usr/local/bin/

# 環境検証実行
export GOSS_USE_ALPHA=1 && goss validate

# JSON形式での結果取得
export GOSS_USE_ALPHA=1 && goss validate --format json
```

### 検証項目

`goss.yaml`で定義している検証項目：

1. **ポート接続性**

   - MySQL (3306)
   - Elasticsearch (9200)
   - Redis (6379)
   - PHP App (8080)
   - React Frontend (5173)

2. **API 正常性**

   - Elasticsearch クラスタ状態が green
   - PHP アプリのヘルスチェックエンドポイント

3. **データベース動作確認**
   - MySQL への接続とクエリ実行
   - Redis への ping 実行

### 意図的な障害テスト

特定のサービスを起動せずに環境検証の動作を確認：

```bash
# Elasticsearchを除いて起動
docker-compose up -d app db redis frontend

# 環境検証（失敗するはず）
goss validate
```

### GitHub Actions

`.github/workflows/test.yml` で CI/CD パイプラインを定義：

1. Docker Compose でサービス起動
2. Goss で環境検証
3. 検証成功後に E2E テスト実行
