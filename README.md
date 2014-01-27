# Y-API Documentation ver 20140127

CSVファイルに保存された商品データを取得するためのRESTful Web API

![rirakkuma](http://www.san-x.co.jp/manage/archive/move_33.jpg)

## API一覧

1. 商品詳細API -> New!
2. カテゴリ一覧API -> New!
3. 商品検索API

## 2. 商品詳細API

### リクエスト形式とURL

* HTTPメソッド：GET
* リクエストURL：/api/v1/item/#{id}

### レスポンス出力スキーマ

#### リクエスト成功時

* result
    * requested
        * id
        * timestamp
    * item_hit 指定したIDの商品が存在した場合は1, 該当する商品が無い場合は0
    * item

#### リクエスト失敗時

* error 
    * code
    * message

### リクエストURL・レスポンス出力の例
リクエストURL：

```
http://php-api.miyahosi720.com/api/v1/item/468469
```

レスポンス出力：

```
{"result":
{"requested":{"id":"468469","timestamp":1390811872},
"item_hit":1,
"item":
{"id":"468469",
"category":{"id":"1022","name":"tv","parent":{"id":"1002","name":"electronics"}},
"title":"アクアヒーター アクエイター 9523",
"price":"9523"}}}
```

エラー時のレスポンス出力の例：

```
{"error":{"code":"400","message":"Requested parameter is not valid"}}
```

## 2. カテゴリ一覧API

### リクエスト形式とURL

* HTTPメソッド：GET
* リクエストURL：/api/v1/categories

### レスポンス出力スキーマ

#### リクエスト成功時

+ result
    + requested
        + timestamp
    + categories
        + parent
            + id
            + name
        + child
            + id
            + name
            + parent_id

#### リクエスト失敗時

* error 
    * code
    * message

### リクエストURL・レスポンス出力の例
リクエストURL：

```
http://php-api.miyahosi720.com/api/v1/categories
```

レスポンス出力：

```
{"result":
{"requested":{"timestamp":1390812045},
"categories":{
"parent":[
{"id":"1001","name":"fashion"},
{"id":"1002","name":"electronics"},
{"id":"1003","name":"office"},
{"id":"1004","name":"eat"},
{"id":"1005","name":"game"},
{"id":"1006","name":"health"},
{"id":"1007","name":"daily"}],
"child":[
{"id":"1011","name":"mens","parent_id":"1001"},
{"id":"1012","name":"refrigerator","parent_id":"1002"},
{"id":"1013","name":"consumables","parent_id":"1003"},
{"id":"1014","name":"food","parent_id":"1004"},
{"id":"1015","name":"ps3","parent_id":"1005"},
{"id":"1016","name":"suppliment","parent_id":"1006"},
{"id":"1017","name":"kitchen","parent_id":"1007"},
{"id":"1021","name":"ladies","parent_id":"1001"},
{"id":"1022","name":"tv","parent_id":"1002"},
{"id":"1023","name":"desc","parent_id":"1003"},
{"id":"1024","name":"drink","parent_id":"1004"},
{"id":"1025","name":"xbox","parent_id":"1005"},
{"id":"1026","name":"kigu","parent_id":"1006"},
{"id":"1027","name":"toilet","parent_id":"1007"},
{"id":"1033","name":"software","parent_id":"1003"},
{"id":"1035","name":"wii","parent_id":"1005"}]
}}}
```


## 3. 商品検索API

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