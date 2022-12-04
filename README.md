# yamldocs

(highly experimental) YAML document describer / markdown generator based on YAML.

## Installation and Usage

yamldocs is built in PHP (cli only) with Minicli. You can run it on a PHP 8.1+ environment or via Docker.

### Installing yamldocs locally
If you prefer to install yamldocs locally, you'll need PHP 8.1 and Composer. Then, clone this repository and install dependencies:

```shell
git clone https://github.com/erikaheidi/yamldocs.git
cd yamldocs
composer install
```

Then you'll be able to run `yamldocs` like this:

```shell
./bin/yamldocs build markdown file=example.yaml output=example.md
```

### Running with Docker
You can run yamldocs with Docker using the `erikaheidi/yamldocs` image and a volume set to share your source files (and output folder) with the container.

The following command will execute yamldocs in a temporary container, generating markdown documents from yaml files located in the `var/yaml` directory and saving the output to `var/output`:

```shell
docker run --rm -v ${PWD}:/work erikaheidi/yamldocs build docs source=/work/var/yaml output=/work/var/output
```

## Example YAML

This YAML demonstrates the structure used to define a document and how the markdown is generated:

```yaml
Section1: #structure is based on the actual yaml
  Item1: value0
  Item2: value1
  Item3:
    - value12
    - value13
  Item4: value2

Section2:
  Item1:
    - value12
    - value13
  Item2: value1
  Item3: value3
  Item4: value2

# The document is described in the _meta node, but there are no required fields. Markdown will be generated anyways,
# based on the structure of the YAML document.
_meta:
  # Each node has a description (info) and an array of items that will be presented as a table.
  Section1:
    info: Information about Section 1
    items:
      Item1: The first row
      Item2: The second row

  Section2:
    info: Information about Section 2
    items:
      Item3: The third row
      Item4: The fourth row
    # Setting up a custom example
    example: |
      Section2:
        Item1:
          - value01
          - value02
```

The following command will generate a markdown document based on the `example.yaml` YAML file, saving it to a file called `example.md` on the current directory:

```shell
./bin/yamldocs build markdown file=example.yaml output=example.md
```

### Generated markdown content:

```markdown
    ## example.yaml
    example.yaml YAML reference
    
    
    ## Section1
    Information about Section 1
    
    ### Reference
    
    | Directive | Expects                 |
    |-----------|-------------------------|
    | Item1     | (String) The first row  |
    | Item2     | (String) The second row |
    | Item3     | (Array)                 |
    | Item4     | (String)                |
    
    
    ### Example
    
    ```yaml
    Section1:
      Item1: value0
      Item2: value1
      Item3:
        - value12
        - value13
      Item4: value2
    
    ```
    
    ## Section2
    Information about Section 2
    
    ### Reference
    
    | Directive | Expects                 |
    |-----------|-------------------------|
    | Item1     | (Array)                 |
    | Item2     | (String)                |
    | Item3     | (String) The third row  |
    | Item4     | (String) The fourth row |
    
    
    ### Example
    
    ```yaml
    Section2:
      Item1:
        - value01
        - value02
    ```
```
 
### Rendered markdown content:

## example.yaml
example.yaml YAML reference


## Section1
Information about Section 1

### Reference

| Directive | Expects                 |
|-----------|-------------------------|
| Item1     | (String) The first row  |
| Item2     | (String) The second row |
| Item3     | (Array)                 |
| Item4     | (String)                |


### Example

```yaml
Section1:
  Item1: value0
  Item2: value1
  Item3:
    - value12
    - value13
  Item4: value2

```


## Section2
Information about Section 2

### Reference

| Directive | Expects                 |
|-----------|-------------------------|
| Item1     | (Array)                 |
| Item2     | (String)                |
| Item3     | (String) The third row  |
| Item4     | (String) The fourth row |


### Example

```yaml
Section2:
  Item1:
    - value01
    - value02
```

## Building multiple docs at once

Use the `build docs` command to build markdown docs for all YAML files in a directory:

```shell
./bin/yamldocs build docs source=var/yaml output=var/output
```

 
