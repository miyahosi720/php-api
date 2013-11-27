# PHPを使ったAPIの作り方

PHPよるAPIの作成を通じながら、実務に近いプログラムを学んでいきます。

[対象] 初級〜中級のエンジニア


## サーバー環境について

AWS + OpsWorks + EC2（PHP Layer in OpsWorks） + custom cookbooksを利用して作成されています。

参考: [devkato/opsworks_custom_cookbook](https://github.com/devkato/opsworks_custom_cookbooks)

また、プロジェクトは以下のパスにdeployされています。

[path] : /srv/www/php_api/current

## サーバー上のプログラムの修正について
基本的にはOpsWorksでのgit経由のdeployをする形にして頂きますが、修正内容をすぐ確認したい等直接編集したい場合には

```sh:
sudo vim xxx.php
```

のようにsudoを利用して下さい。新しくファイルを作成する場合でも同様です。x


## 商品データについて

item.csvには、ランダムに作成した商品データが100件含まれています。

それぞれのカラムの値や型は以下の通りです。

- 商品ID : Integer（1001 〜 1100）
- カテゴリID : Integer（1000001 〜 1000010）
- 商品名 : String
- 価格 : Integer
