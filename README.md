## How to deploy to Patr

Make sure you're in the root directory of this repository.
Make sure Docker is running.

- Create a Docker Repository on Patr: https://app.patr.cloud/docker-repository

- Once created, copy the repository's full `Repo Name` from within Patr (let's call that `repoName`)

- Create a Deployment on Patr: https://app.patr.cloud/deployment

When creating a deployment, choose the Patr Registry, and use the `repoName` we just created above. For tag, enter `stable`.

For ports, use port `80`, when prompted. The remaining values can be left at sane defaults.

- Make sure you're logged into Patr registry on Docker:

```bash
docker login registry.patr.cloud
```

When prompted, use the username and password used to login to Patr.

- Build your image:

```bash
docker build . -t repoName:stable
```

- Push your image:

```bash
docker push repoName:stable
```

## Pushing updates to Patr.

Simple. Update your code, and just repeat the last two steps :P