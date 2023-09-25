# IS-API
This API allows clients to upload, map and import objects from CSV and XLSX files.
The file is uploaded to a AWS bucket and processed by a message system. For the test, this is running immediately, but is possible to setup an external transport.

## Usage
For the test, the database and AWS S3 credentials must be filled on the .env file.
```bash
DATABASE_URL="mysql://<USER>:<PASSWORD>@<IP>:3306/<DATABASE_NAME>?serverVersion=10.11.2-MariaDB&charset=utf8mb4"

### AWS S3 ###
AWS_BUCKET_NAME=
AWS_KEY=
AWS_SECRET_KEY=
AWS_S3_REGION=
```

### To run it locally:
```bash
# install dependencies
composer install

# load fixtures
symfony console doctrine:fixtures:load

# start local server
symfony server:start
```

### To run unit tests:
```bash
php bin/phpunit
```