# Cekta/Skeleton

skeleton for create you api service DRAFT

## Usage

```
git clone https://github.com/cekta/skeleton.git {you_project_name}
cd {you_project_name}
make dev
```

### requirements to run:
1. git
2. docker
3. Make

### Update

after change constructor classes or package dependencies to apply:

```
make build
```

or just restart 

```
make dev
```

### FULL Update

after update from repository you can use:

```
make refresh
make dev
```

### Shell

you can open dev shell

```
make shell
```

after this command you can use composer and other tools.

### Test

run all dev tests (check code style, static analyze, etc) in dev container

```
composer test
```

### production build

to make docker image

```
make image
```

after create image you can push to your repo.

recommend set custom tag and link to repository.

use this command in ci/cd.