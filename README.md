## FLYERALARM - Simple Reseller

The purpose of this repository is to demonstrate how the
FLYERALARM Reseller API can be used to order products.

For further details and questions, please contact esolutions@flyeralarm.com

### Get it running

@GOTO ../config/default.php
insert your credentials

#### Run locally

run:

```bash
# composer install
```

run:

```bash
# cd public
# php -S localhost:8090
```

#### Run with Docker

```bash
# docker build . -t phpfaapi && docker run -p8090:8000 -d phpfaapi
```

Navigate to:
<http://localhost:8090/>