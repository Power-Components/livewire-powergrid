---
name: Bug report
about: Report bugs or unexpected software behavior
title: 'Bug Report: ** short & meaningfultitle describing the bug ***'
labels: bug
assignees: luanfreitasdev

---

# PowerGrid Bug Report


Thank you for reporting a bug and helping us to improve PowerGrid!

## Guidelines

`   🐛 `   We use GitHub Issues exclusively for tracking bugs and unexpected software behavior. 

`   🙏 `  Please use the [Discussions](https://github.com/Power-Components/livewire-powergrid/discussions) tab for questions like _"How to...",_ "_how can I..."_ .

`   ✍️ `  Give this report a short but meaningful title. Make it easy to spot for others who might be facing the same issue.

`   ⚠️ `  Issues that do not describe a bug or do not follow the template will be closed.

## Information

### Pre-steps

#### Have you searched through other issues to see if your problem is already reported or has been fixed?
- [ ] Yes - I did not find it.
- [ ] No

#### Did you read the [documentation](https://livewire-powergrid.com/)?
- [ ] Yes - I did not find it.
- [ ] No

#### Have you tried to publish the views?

You can publish the views to make sure there is no "old code" trapped in views which are not up-to-date.

To publish, run: `php artisan vendor:publish --tag=livewire-powergrid-views`

- [ ] Yes - I didn't work.
- [ ] No, this error is not related to views.

#### Is there an error in the console?
- [ ] Yes - I'll add a screenshot or report it below.
- [ ] No

### Software Version

You can run `composer show -i` and `npm list` to list installed package with their versions.

| Software  | Version (exactly) |
|-----------|-------------------|
| PowerGrid |                   |
| Laravel   |                   |
| Livewire  |                   |
| Alpine JS  |                   |

### Theme
- [ ] Tailwind 2.x
- [ ] Tailwind 3.x
  - [ ] With tailwind/forms
- [ ] Bootstrap - Version:        

---
## Describe the bug

### What happened?

I did FOO expecting BAR as a result. However, I received BAZ...

### To Reproduce...

First click on "FOO" then....

### Suggestions

(Do you have any idea how we can fix it?)


## Extra information

**Screenshots**

**Code snippet**

```php 
<?php

//...
```
