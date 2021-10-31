<div align="center">
	<p><img  src="art/header.jpg" alt="PowerGrid Logo"></p>
</div>

------

# How to contribute with PowerGrid

This article's objective is to facilitate the process of collaborating with  [PowerGrid](https://github.com/Power-Components/livewire-powergrid).

## 1) Fork the project
To fork PowerGrid, you simply have to access the repository's page [PowerGrid](https://github.com/Power-Components/livewire-powergrid) and click *Fork*:<br />
<img src="img/click-to-fork.png" /><br />
As a result, you should have something similar to https://github.com/cpereiraweb/livewire-powergrid.<br />
Note: If you are a member of different teams or organizations, you must select which account you will be forking to.

## 2) Clone your fork repository to your computer
Select or create a new folder at your computer where you want to store project files:<br />
<img src="img/cloning-repo.png">

## 3) Create a new branch and push it to your repository
Access the repository's folder:
```bash
cd livewire-powergrid
```
Create a new branch (For instance, my own name as the branch name):
```bash
git checkout -b cpereiraweb
```
You need to push this branch to the server. As a suggestion, create a TODO.md file at the project's root. Add it and commit.
```bash
touch TODO.md
git add .
git commit -m "Added TODO.md"
git push origin cpereiraweb  # Use your branch name here, ok?
```
## 4) Add PowerGrid to your Laravel project
Open your Laravel's project and install the component as described in its documentation.
```bash
composer require power-components/livewire-powergrid
```
## 5) Configure ```composer``` to use your own PowerGrid clone
You will need to inform `composer` that you want to use a developing version of this package and where is this version located.
This is very simple to do:
Open composer.json file and locate the PowerGrid requirement line:
```json
"power-components/livewire-powergrid": "^1.0",
```
Following what I have done so far, I will configure the version which must be `dev-`+`your-branch-name`. Following our example: `dev-cpereiraweb`:
```json
"power-components/livewire-powergrid": "dev-cpereiraweb",
```
The second step is to inform where is the repository located. To do so, we add the section `repositories` to the `composer.json` file. 
In this section you can inform either your Github repository URL or indicate its path on your local machine. 

The example below uses the GitHub repository URL:
```json
        ...
        "spatie/laravel-ray": "^1.17",
        "wulfheart/pretty_routes": "^0.2.0"
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/cpereiraweb/livewire-powergrid.git"
        }
    ],
    "autoload": {
        ...
```

And, the next example shows the version with your local path:
```json
        ...
        "spatie/laravel-ray": "^1.17",
        "wulfheart/pretty_routes": "^0.2.0"
    },
    "repositories": [
        {
            "type": "path",
            "url": "Z:\\Code\\forks\\PowerComponents\\livewire-powergrid"
        }
    ],
    "autoload": {
        ...
```

Note: The example above shows a Windows OS path. If you are a `WSL`, `Docker`, `Linux` or `MacOS` user, you must adapt this path to your system's format. 
For example: `/home/cpereiraweb/code/forks/PowerComponents/livewire-powergrid`.

## 6) Run `composer update` command
Now, just run `composer update` command inside your Laravel project so composer can update its references. See below the result using the repository link:<br />

<img src="img/composer-update.png"><br />
The image shows the commits at my repository. First, I had installed the commit  `219cdd8`.  Now, at the image above you can see that it has been updated to the commit `65ae610`.

If you had your `composer.json` configured to use a local path instead, you should have a result similar to this:<br/>
<img src="img/composer-update-local-repo.png"><br />&nbsp;<br />

You may notice that the GitHub's version was removed and it was replaced by the local version. The commit indications are no longer displayed. With this configuration, I am not required to `push` my fork nor run `composer update` in my Laravel Project. It is a more dynamic process.

## It's all set!
Now, just follow the regular flow of any Git project. Perform any changes to your own fork and push to Github.
Always run `composer update` inside your Laravel project to update the codes used in your project.
That's it. Clean procedure and no MOP*.

\*(*[MacGyver](https://www.lexico.com/definition/macgyver)-oriented Programming*)
