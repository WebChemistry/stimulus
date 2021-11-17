## Installation

```neon
extensions:
	- WebChemistry\Stimulus\DI\StimulusExtension
```

## Usage in Latte

**Controller**

```html
<div n:stimulus="stimulusController('firstController', values: [bool: true], classes: [hidden: 'invisible'])"></div>
```

output
```html
<div
	data-controller="first-controller"
	data-first-controller-bool-value="1"
	data-first-controller-hidden-class="invisible"
></div>
```

**Target**

```html
<div n:stimulus="stimulusTarget('controller', 'output')"></div>
```

output
```html
<div
	data-controller-target="output"
></div>
```

**Action**

```html
<div n:stimulus="stimulusAction('firstController#action', parameters: [bool: true])"></div>
```

output
```html
<div
	data-action="first-controller#action"
	data-first-controller-bool-param="1"
></div>
```

Multiple controllers, actions, targets etc.

```html
<div n:stimulus="stimulusController('firstController'), stimulusController('secondController'), stimulusTarget('another', 'output')"></div>
```

output
```html
<div
	data-controller="first-controller second-controller"
	data-another-target="output"
></div>
```

Namespace

```html
<div n:stimulus="stimulusController('namespace/firstController'), stimulusController('namespace--second-controller')"></div>
```

output
```html
<div
	data-controller="namespace--first-controller namespace--second-controller"
></div>
```
