# Running the Project
This project is fully supported using Docker and can be run with zero configuration.

### Build Docker Images
```bash
docker compose build
```

### Launch Docker Containers
```bash
docker compose up -d
```
The project will be accessible at [http://localhost:3010](http://localhost:3010)  
and Mailpit UI can be accessed at [http://localhost:8125](http://localhost:8125)


Additionally, this project has implemented Continuous Deployment, and the latest version can be accessed at
[https://remindme.azizramdan.id](https://remindme.azizramdan.id)  
and Mailpit UI can be accessed at [https://mailpitui-remindme.azizramdan.id](https://mailpitui-remindme.azizramdan.id)

----
# RemindMe - Laravel Challenge

Welcome to Nabitu take home challenge!

In this repository you will find API specification & scaffolding code for the web app named `RemindMe`.

`RemindMe` is a simple web app that allows users to create reminder for their schedules. It will send email notification to the user when the reminder is due.

You can check the API specification for this web app in [`rest_api.md`](./docs/rest_api.md).

## Your Mission

1. Build the web app based on specification written in `README.md` and [`rest_api.md`](./docs/rest_api.md). **Treat it as an MVP**. For the backend you must use **[Laravel Framework](https://laravel.com/)**. For the frontend you can use any framework you like or even just vanilla HTML, CSS, & Javascript. You can use [Laravel Blade](https://laravel.com/docs/10.x/blade) as well but make sure it completely uses the REST API.
2. Dockerize your system & make sure it can run with full functionality using [Docker Compose](https://docs.docker.com/compose/) in Linux-like environment. Make sure when we execute either `docker compose up --build` or `sail up --build` command, we don't need to do any extra steps (e.g configuring `.env`, building frontend, etc...) to make your system works. We will review your system in Ubuntu or MacOS.
3. Write automated testing for your backend. At the very minimum you must implement unit testing (not feature testing). If you can write automated testing for your frontend as well, that would be great.
4. Implement CI pipeline for your system. We recommend using [Github Actions](https://github.com/features/actions), but you can use any CI tool you like. Make sure to leverage docker compose in your CI pipeline.

## Evaluation

We will evaluate your submission based on these criteria:

1. The quality of your web app experience from end-user perspective.
2. The correctness of your implementation according to the specification docs.
3. Your choice of tradeoffs during development based on both business & technical perspective.
4. Readability, maintainability, & testability of your code.
5. The quality of your workflow when using Github to develop the web app. This includes the quality of your commit messages, pull requests, & branch naming.

## Submission

1. Fork this repository & do your work in your own forked repository.
2. Submit your CV in PDF along with the URL of your forked repository to [this page](https://ghazlabs.com/nabitu/senior-backend-engineer-laravel.html).
3. We will review your submission & get back to you as soon as possible.

> **Note:**
>
> If you have any questions regarding this challenge, please don't hesitate to open an issue in this repository.