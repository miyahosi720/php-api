# Y-API Documentation ver 20131223

CSVファイルに保存された商品データを取得するためのRESTful Web API

![rirakkuma](http://www.san-x.co.jp/manage/archive/move_33.jpg)

## API一覧

1. 商品検索API
2. 商品詳細API

## 1. 商品検索API

### リクエスト形式とURL

* HTTPメソッド：GET
* リクエストURL：/api/v1/items

### リクエストパラメーター

|パラメーター|型|必須|値|
|---|---|---|---|
|format|String|-|jsonもしくはxmlで指定。指定がない場合はjson形式で出力されます。
|category_id|Integer|-|カテゴリIDを指定。
|price_min|Integer|-|価格の下限を指定。
|price_max|Integer|-|価格の上限を指定。
|sort|String|-|ソートの種別を指定。−（降順）もしくは＋（昇順）で並び順を指定。+id（ID昇順）、-id（ID降順）、+price（価格昇順）、-price（価格降順）のいずれか。UTF-8にエンコードされている必要あり。（例：価格昇順 sort=%2Bprice）
|count_per_page|Integer|-|ページ送り用。1ページに表示する商品数を指定。
|page_number|Integer|-|ページ送り用。何ページ目を表示するかを指定。

* count_per_pageとpage_numberは両方のセットが必要で、片方だけのセットはパラメーターのエラーになります。

### レスポンス出力スキーマ

#### リクエスト成功時

* result
    * requested
        * parameter
            * format
            * category_id
            * price_min
            * price_max
            * sort
            * count_per_page
            * page_number
        * timestamp
    * item_count
        * returned
        * available
    * item

#### リクエスト失敗時

* error 
    * code
    * message

### エラーコードおよびメッセージ

リクエストに失敗した場合は、errorパラメーターに下記のステータスコードとメッセージがセットされ出力されます。

|ステータスコード|エラーメッセージ|状況
|---|---|---|
|400|Requested parameter is not valid|リクエストパラメーターの形式エラー
|404|The url you requested was not found|指定されたリソースが見つからない
|405|Your HTTP method is not allowed|GET以外のHTTPメソッドを使用している
|500|Server Error|システムエラー

### リクエストURL・レスポンス出力の例
リクエストURL：

```
http://php-api.miyahosi720.com/api/v1/items?format=xml&category_id=1000004&price_min=1000&price_max=8000&sort=%2Bprice&count_per_page=3&page_number=2
```

レスポンス出力：

```
<result>
<requested>
<parameter>
<format>xml</format>
<category_id>1000004</category_id>
<price_min>1000</price_min>
<price_max>8000</price_max>
<sort>+price</sort>
<count_per_page>3</count_per_page>
<page_number>2</page_number>
</parameter>
<timestamp>1387773773</timestamp>
</requested>
<item_count>
<returned>3</returned>
<available>8</available>
</item_count>
<item>
<product_id>1069</product_id>
<category_id>1000004</category_id>
<title>商品その069</title>
<price>5291</price>
</item>
<item>
<product_id>1034</product_id>
<category_id>1000004</category_id>
<title>商品その034</title>
<price>7098</price>
</item>
<item>
<product_id>1062</product_id>
<category_id>1000004</category_id>
<title>商品その062</title>
<price>7163</price>
</item>
</result>
```

エラー時のレスポンス出力の例：

```
<error>
<code>400</code>
<message>Requested parameter is not valid</message>
</error>
```

## 2. 商品詳細API

### リクエスト形式とURL

* HTTPメソッド：GET
* リクエストURL：/api/v1/item

### リクエストパラメーター

|パラメーター|型|必須|値|
|---|---|---|---|
|format|String|-|jsonもしくはxmlで指定。指定がない場合はjson形式で出力されます。
|product_id|Integer|○|商品IDを指定。

### レスポンス出力スキーマ

#### リクエスト成功時

* result
    * requested
        * parameter
            * format
            * product_id
        * timestamp
    * item_hit 指定したIDの商品が存在した場合は1, 該当する商品が無い場合は0
    * item

#### リクエスト失敗時

* error 
    * code
    * message

### エラーコードおよびメッセージ

商品検索APIのエラーコードおよびメッセージと同じ。

### リクエストURL・レスポンス出力の例
リクエストURL：

```
http://php-api.miyahosi720.com/api/v1/item?format=json&product_id=1007
```

レスポンス出力：

```
{"result":
{"requested":
{"parameter":{"format":"json","product_id":"1007"},
"timestamp":1387766677},
"item_hit":1,
"item":{"product_id":"1007","category_id":"1000005","title":"\u5546\u54c1\u305d\u306e007","price":"4513"}}}
```

エラー時のレスポンス出力の例：

```
{"error":{"code":"404","message":"The url you requested was not found"}}
```