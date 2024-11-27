# AmazingBlog

Simple api to manage Posts

## get started
```shel
git clone https://github.com/hassanesidiammi/amazinblog.git
cd amazinblog
docker-compose up -d

```
Wait docker containers to be ready 

```shel
docker-compose exec php bash
composer install
symfony console doctrine:mongodb:schema:update

## Fix file permissions
chown -R www-data:www-data var/
chown -R www-data:www-data config/jwt/

## Generate public/private keys
lexik:jwt:generate-keypair

exit
```

## Sign up (Create a user)

Now you can use the api, lets start by sign up

```shel
curl --request POST \
  --url http://localhost/api/register \
  --header 'Content-Type: application/json' \
  --header 'User-Agent: insomnia/10.1.1' \
  --data '{
	"name": "EditorName",
	"email": "edito@test.com",
	"password": "123456"
}'
```

the respons contains user information and a generated token
```json
{
   "user":{
      "id":1,
      "name":"EditorName",
      "email":"edito@test.com",
      "roles":[
         "ROLE_EDITOR",
         "ROLE_USER"
      ]
   },
   "token":"<token>"
}
```

### Sign In
An existing user can login and get a new token

```console
curl --request POST \
  --url http://localhost/api/login \
  --header 'Content-Type: application/json' \
  --header 'User-Agent: insomnia/10.1.1' \
  --data '{
    "username": "edito@test.com",
    "password": "123456"
}'

```

### Create new Post

```console
curl --request POST \
  --url http://localhost/api/posts \
  --header 'Authorization: Bearer <token>' \
  --header 'Content-Type: application/json' \
  --header 'User-Agent: insomnia/10.1.1' \
  --data '{
  "title": "Api with symfony",
  "content": "Use docker, mongodb, react and bootstrap too!"
}'
```

Other actions are like that :D


## Use the client

*node 20 or higher is required*

```console
git clone https://github.com/hassanesidiammi/amazinblog_client.git
npm install
npm start
```
go to http://localhost:3000/login
and register or use an existing user to login

- edito@test.com
- 123456

![amazingblog](https://github.com/user-attachments/assets/ec9b8a78-cec6-46ec-9fce-4345c16daf7e)

Done!
