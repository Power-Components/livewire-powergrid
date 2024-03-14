<div align="center">
	<p><img  src="art/header.jpg" alt="PowerGrid Logo"></p>
</div>

------

# PowerGrid Contribution Guide

`💓` **Thank you for your interest in contributing to PowerGrid.**

Contributions are welcome and appreciated. There are many different ways to contribute to this project.

## Contribution areas

Here are some ways you can contribute and make an impact on PowerGrid.

- [Community building](#community-building)
- [Design](#design)
- [Translation](#translation)
- [Documentation](#documentation)
- [Issues and Support](#issues-and-support)
- [Demo Code](#demo-code)
- [Code Contribution](#code-contribution)

### Community building

PowerGrid relies on a community of users and contributors. You can help build a stronger community by:

- Participating in the [Discussions](https://github.com/Power-Components/livewire-powergrid/discussions) tab.
- Write an article, tutorial, or record a YouTube video using PowerGrid.
- Creating social media posts with PowerGrid content. You can talk about features, updates, and releases.
- Sharing your code skills by helping newcomers and mentoring other users.

### Design

We try to grow the PowerGrid brand in a professional and impactful way.

We appreciate your help with:

- Creating artwork and improving the package's branding and presentation material.
- Creating promotional and marketing materials for social media, video, and web content.
- Providing or improving table components style.

### Translation

- PowerGrid is used by people in different parts of the world. You can help with translation (localization) and help the project better adapt to your community.

### Documentation

To be successful, an open-source project must have high-quality documentation.

We appreciate your help with:

- Try, review, and suggest changes to improve the user’s experience with any PowerGrid documentation content.
- Maintain the README and CONTRIBUTING guides.
- Adding, improving, or correcting the documentation directly in the [Power-Components/powergrid-doc](https://github.com/Power-Components/powergrid-doc) repository.

## Demo Code

- You can help by providing new use cases or improving the existing ones directly in the [Power-Components/powergrid-demo](https://github.com/Power-Components/powergrid-demo).

### Issues and Support

You can help the PowerGrid Team and contributors with handling open Issues and support requests.

- Test, reproduce, and validate reported bugs.
- Answer user questions in the [Issues](https://github.com/Power-Components/livewire-powergrid/issues) and [Discussions](https://github.com/Power-Components/livewire-powergrid/discussions) tabs.

### Code Contribution

 To build and test the code, follow the steps described in this section.

1 .**Fork**

```shell
git clone https://github.com/Power-Components/livewire-powergrid.git && cd livewire-powergrid
```

Install all dependencies with composer and NPM.

```shell
composer install
```

Then run:

```shell
npm install
```

<br/>

2. **Create a new branch**

Create a new branch specifying `feature`, `fix`, `enhancement`.

```shell
git checkout -b feature/my-new-feature
```

<br/>

3. **Code and check your work**

Write your code and, when you are done, run the CS Fix:

```Shell
composer fix
```

Run tests and static analysis:

```Shell
composer verify
```

<br/>

4. **Build Assets**

If you have updated or added JavaScript code, you need to recompile the assets and include it in your commit.

```Shell
npm run build
```

<br/>

5. **Tests**

Including tests is not mandatory, but if you can write tests, please consider doing it.

Besides all technical benefits, tests also help to prove your concept and make the maintainers' job easier. PowerGrid is developed entirely by volunteers.

<br/>


6. **Commit**

Please send clean and descriptive commits.

<br/>


7. **Pull Request**

Open a Pull Request (PR) and use the template to detail your changes and motivations. Please make only one change per Pull Request.

If you have never written a PR before, see this excellent [example](https://github.com/Power-Components/livewire-powergrid/pull/149) by [@vs0uz4](https://github.com/vs0uz4) for inspiration.

<br/>

<hr/>

If you have any questions, do not hesitate to reach out to the community in the repository [Discussions](https://github.com/Power-Components/livewire-powergrid/discussions) tab.

Thank you,

⚡ The PowerGrid Team and [Contributors](../../contributors)
