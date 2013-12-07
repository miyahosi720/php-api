# Y-API ver1.0 Documentation

CSVファイルに保存された商品データを取得するためのREST API

## 商品データについて

item.csvには、ランダムに作成した商品データが100件含まれています。

それぞれのカラムの値や型は以下の通りです。

* 商品ID : Integer（1001 〜 1100）
* カテゴリID : Integer（1000001 〜 1000010）
* 商品名 : String
* 価格 : Integer

## API一覧

* 商品検索API
* 商品詳細API

## 商品検索API

### リクエスト形式とURL

HTTPメソッド:GET

|データ形式|リクエストURL|
|---|---|
|json|/y-api/v1/SearchItems.json|
|xml|/y-api/v1/SearchItems.xml|

### リクエストパラメーター

|パラメーター|型|必須|値|
|---|---|---|---|
|category_id|Integer|-|カテゴリIDを指定。
|price_min|Integer|-|価格の下限を指定。
|price_max|Integer|-|価格の上限を指定。
|sort|String|-|ソートの種別を指定。id_desc（IDを基準に降順）、id_asc（IDを基準に昇順）、price_desc（価格を基準に降順）、price_asc（価格を基準に昇順）のいずれかから選択。
|count_per_page|Integer|△|ページ送り用。1ページに表示する商品数を指定。
|page_number|Integer|△|ページ送り用。何ページ目を表示するかを指定。

* count_per_pageとpage_numberは両方のセットが必要で、片方だけのセットはパラメーターのエラーになります。

### リクエストURL・レスポンス出力の例
リクエストURL：


    http://php-api.miyahosi720.com/y-api/v1/SearchItems.json?category_id=1000004&price_min=1000&price_max=8000&sort=price_desc&count_per_page=4&page_number=2


レスポンス出力：

```
{"item":
[{"product_id":"1069","category_id":"1000004","title":"\u5546\u54c1\u305d\u306e069","price":"5291"},
{"product_id":"1057","category_id":"1000004","title":"\u5546\u54c1\u305d\u306e057","price":"4701"},
{"product_id":"1020","category_id":"1000004","title":"\u5546\u54c1\u305d\u306e020","price":"3095"},
{"product_id":"1011","category_id":"1000004","title":"\u5546\u54c1\u305d\u306e011","price":"2457"}],
"item_count":4,
"requested":
{"action":"SearchItems",
"format":"json",
"url":"http:\/\/php-api.miyahosi720.com\/y-api\/v1\/SearchItems.json?category_id=1000004&price_min=1000&price_max=8000&sort=price_desc&count_per_page=4&page_number=2"},
"timestamp":1386412005}
```

### エラーコードおよびメッセージ

エラーが起こった際は、出力されたerrorパラメーターに、下記のステータスコードとメッセージがセットされます。

|HTTPステータスコード|エラーメッセージ|状況
|---|---|---|
|400|Bad Request|リクエストパラメーターのエラー
|404|The URL You Requested Was Not Found|指定されたリソースが見つからない
|405|Method Not Allowed|GET以外のHTTPメソッドを使用している
|500|Server Error|システムエラー

エラー時のレスポンス出力の例：

```
{"error":{"code":"404","message":"The URL You Requested Was Not Found"}}
```

## 商品詳細API

### リクエスト形式とURL

HTTPメソッド:GET

|データ形式|リクエストURL|
|---|---|
|json|/y-api/v1/LookUpItem.json|
|xml|/y-api/v1/LookUpItem.xml|

### リクエストパラメーター

|パラメーター|型|必須|値|
|---|---|---|---|
|product_id|Integer|○|商品IDを指定。存在しない商品IDの指定は商品0件が返る。

### リクエストURL・レスポンス出力の例
リクエストURL：

```
http://php-api.miyahosi720.com/y-api/v1/LookUpItem.json?product_id=1007
```

レスポンス出力：

```
{"item":[{"product_id":"1007","category_id":"1000005","title":"\u5546\u54c1\u305d\u306e007","price":"4513"}],
"item_count":1,
"requested":
{"action":"LookUpItem",
"format":"json",
"url":"http:\/\/php-api.miyahosi720.com\/y-api\/v1\/LookUpItem.json?product_id=1007"},
"timestamp":1386414649}
```
### エラーコードおよびメッセージ

商品検索APIのエラーコードおよびメッセージと同じ