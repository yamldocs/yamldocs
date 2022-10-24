## apko
apko YAML reference


## contents
This section defines the software sources and packages that will compose this image.

### Reference
| Directive    | Expects |                                               |
|--------------|---------|-----------------------------------------------|
| repositories | Array   | The repository sources where to find APKs.    |
| packages     | Array   | The list of packages required for this image. |



### Example

```yaml
repositories:
    - 'https://dl-cdn.alpinelinux.org/alpine/edge/main'
    - 'https://dl-cdn.alpinelinux.org/alpine/edge/community'
packages:
    - alpine-baselayout
    - php81
    - php81-common

```
## entrypoint
This section defines the entrypoint for this image.

### Reference
| Directive | Expects |                                                                    |
|-----------|---------|--------------------------------------------------------------------|
| command   | String  | The command that should be executed as entry point for this image. |



### Example

```yaml
command: /usr/bin/php81

```
## environment
Environment variables that will be set.

### Reference
| Directive | Expects |                         |
|-----------|---------|-------------------------|
| PATH      | String  | Sets the $PATH variable |



### Example

```yaml
PATH: '/usr/sbin:/sbin:/usr/local/bin:/usr/local/sbin'

```
## accounts
User accounts and groups that should be present by default.

### Reference
| Directive | Expects |                                                                               |
|-----------|---------|-------------------------------------------------------------------------------|
| groups    | Array   | Defines system groups.                                                        |
| users     | Array   | Defines system users.                                                         |
| run-as    | String  | Default user that will execute the command defined in the entrypoint section. |



### Example

```yaml
groups:
    - { groupname: nonroot, gid: 65532 }
users:
    - { username: nonroot, uid: 65532 }
run-as: 65532

```