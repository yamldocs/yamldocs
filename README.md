# yamldocs
[![PHP Composer](https://github.com/erikaheidi/yamldocs/actions/workflows/php.yml/badge.svg)](https://github.com/erikaheidi/yamldocs/actions/workflows/php.yml)

Yamldocs is a markdown document generator based on YAML files, written in PHP with Minicli. It can be used as a standalone app or included as a Composer bin command to be used within existing projects. It is useful to create automated reference docs that can be customized through templates and a common builder interface.

Check the [documentation website](https://yamldocs.dev) for installation and usage instructions.

## Examples

### Example YAML

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

### Building a single document

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

### Building multiple documents

Use the `build docs` command to build markdown docs for all YAML files in a directory. Add `--recursively` to build subdirs.

```shell
./bin/yamldocs build docs source=var/yaml output=var/output --recursive
```

Check the full docs at https://yamldocs.dev.
 
