## ddns api 

cloudflare を ddns で使うためのセット



## 使い方

cloudflare の api を通して DNSのAレコードを更新するAPIを呼び出す。

```shell
HOST=my-ddns.example.tld
domain=random.example.tld
ip=1.1.1.1
app_token=abc # config('app.token')を使う。
## 
curl https://${HOST}/ddns/${domain}/${op}?token=${app_token}
```

## 設定

`.env`を使って設定する。
```shell
CF_TOKEN_PATH=storage/credentials/cloudflare_token.json.enc
APP_TOKEN=xxxx.xxxx.xxxx
```

storage/credentials/cloudflare_token.json
```json
[
  {
    "domain": "example.tld",
    "key": "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
  },
  {
    "domain": "example.gtd",
    "key": "xxxxxxxxxxxxxxxxxxxxx"
  }
]
```

ファイルを暗号化して保存して使う場合

```shell
i=$(( 1000* 1000 ))
file_in=storage/credentials/cloudflare_token.json
file_out=storage/credentials/cloudflare_token.json.enc
passphrase="my_strong_password"


openssl enc -e -aes-256-cbc \
  -pbkdf2 -iter "${i}" \
  -in "${file_in}" -out "${file_out}" \
  -k "${passphrase}"\
  -base64 \
  ;
  
```

```php
$str = openssl_equivalent_decrypt($token_path, config('app.token'));
```

