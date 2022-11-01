# yamldocs

(highly experimental) YAML document describer / markdown generator based on YAML.

## Requirements

- PHP >=8.1

### Installation and Execution

You can run yamldocs with Docker, using the `minicli/php81` image as follows:

```shell
docker run --rm -v ${PWD}:/work minicli/php81 /work/yamldocs build markdown file=/work/example.yaml output=/work/example.md
```

If you prefer to install it locally, you'll need PHP 8.1 and Composer. Then, clone this repository and install dependencies:

```shell
git clone https://github.com/erikaheidi/yamldocs.git
cd yamldocs
composer install
```

Then you'll be able to run `yamldocs` like this:

```shell
./yamldocs build markdown file=example.yaml output=example.md
```
### Example YAML

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
./yamldocs build markdown file=example.yaml output=example.md
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

 