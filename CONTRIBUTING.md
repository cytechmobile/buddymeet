# Contribution Guidelines

Thank you for taking the time to contribute! Your input is deeply valued, whether an issue, a pull request, or even feedback, regardless of size, content or scope.

## Table of contents

[Getting started](#getting-started)

[Code of Conduct](#code-of-conduct)

[Reporting an issue](#reporting-an-issue)

[Creating a pull request](#create-a-pull-request)

[Branch naming convention](#branch-naming-convention)

[Coding Guidelines](#coding-guidelines)

[License](#license)

[Questions](#questions)

## Getting started

Please refer the project [documentation](readme.txt) for a brief introduction. 

## Code of Conduct

A [Code of Conduct](CODE_OF_CONDUCT.md) governs this project. Participants and contributors are expected to follow the rules outlined therein.

## Reporting an issue

This project uses GitHub to track issues, feature requests, and bugs. Before submitting a new issue, please check if the issue has already been reported. If the issue already exists, please upvote the existing issue 👍.

For new feature requests, please elaborate on the context and the benefit the feature will have for users, developers, or other relevant personas.

## Creating a pull request

This repository uses the [GitHub Flow](https://docs.github.com/en/get-started/quickstart/github-flow) model for collaboration. To submit a pull request follow the steps below.

### Fork the project

Create a personal copy ("fork") of the repository. This gives you a personal workspace where you can safely make changes without affecting the original codebase.

### Create a branch 

Create a new branch in your forked repository. This branch will contain your changes and should be named according to the [branch naming convention](#branch-naming-convention) below.

### Make changes

Make your changes in the new branch. This could involve fixing bugs, adding features, or improving documentation. Ensure that your changes are well-structured and follow the [coding guidelines](#coding-guidelines) outlined below.

* **Commit your changes:** Commits should be atomic and semantic. Commit your changes with a clear and descriptive commit message. This helps others understand the purpose of your changes. This project follows the [Conventional Commits specification](https://www.conventionalcommits.org), a simple convention that enforces a clear, structured commit history.

* **Run tests:** This project doesn't currently have any automated tests. That would be a very welcome contribution! However, if you are making changes that could affect the functionality of the project, please ensure that you manually test your changes to verify they work as intended.

* **Lint your code:** Please check the [Coding Guidelines](#coding-guidelines) to catch style issues and enforce our coding standards.

* **Check documentation:** If you have made changes that affect the documentation, update it accordingly.

### Create a pull request

Once you are satisfied with your changes, push your branch to your forked repository on GitHub. Then, navigate to the original repository and create a pull request (PR) from your branch. This PR will notify the project maintainers of your changes and allow them to review and discuss them. In the PR description, please link the relevant issue (if any), provide a detailed description of the change, and include any assumptions.

### Review process

The project maintainers will review your PR. They may ask for changes or provide feedback. Be prepared to address any comments or suggestions they have. If you need to make changes, commit them to the same branch in your forked repository. The PR will automatically update with your new commits. 

### Merge the pull request

Once your PR is approved, it can be merged into the main branch of the original repository. If you have write access, you can merge it yourself. Otherwise, a project maintainer will handle the merge.

### Sync your fork

After your PR is merged, it is a good practice to sync your forked repository with the original repository. This ensures that your fork remains up-to-date with the latest changes.

### Congrats yourself

Congratulations! 🎉 You are now an official contributor to this project! We are grateful for your contribution.

## Branch naming convention

When creating a new branch, please follow the [Conventional Commits specification](https://www.conventionalcommits.org). Prefix the branch name with the type of change you are making, such as `fix`, `feat`, `docs`, `chore`, etc. This helps in categorizing changes and understanding the purpose of the branch at a glance. Then, follow it with a short description of the change. For example: `fix/<short_descriptive_name>` or `feat/<short_descriptive_name>`.

## Coding Guidelines

This project follows the [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/). Please ensure that your code adheres to these standards. 

## License

By contributing, you agree that your contributions will be licensed under the project's [LICENSE](LICENSE) file.

## Questions

If you have any questions, feel free to reach out to the project maintainers by opening an issue. We're here to help!