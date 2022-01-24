<h1 align="center">Sortir.com</h1>
<p align="center">✨ Developped by <strong>TheDreamTeam</strong> ✨</p>  

## Installation
**Require PHP 7.4.26.**

    git clone https://github.com/boutterudy/sortir.com.git  
    cd sortir.com  
    composer install

## Git usage
To create a cleaner history of modifications, as explained [here](https://medium.com/@peterjussi/a-basic-git-workflow-for-smaller-projects-d8694d50297d#8d1a), you should run that command to configure git auto setup rebase:
``git config --global branch.autosetuprebase always``

On this project, we're using two mains branches:
1. ``production``: production branch, containing only tested and working code
2. ``preproduction``: preproduction branch, this is where we're testing our code before merging it to ``production`` branch

To develop a new feature, we're creating a new branch based on ``preproduction`` branch using these commands:

    git pull origin preproduction
    git checkout -b feature/exampleName preproduction

Replace ``exampleName`` by an explicit name of your feature.

When your feature is ready to push, you can commit and push:

    git commit -m "Simple description of what you've done"
    git push origin feature/exampleName

Then finally merge your new feature to preproduction branch:
    
    git checkout preproduction
    git fetch
    git pull origin preproduction
    git merge feature/exampleName
    git branch -d feature/exampleName
    git push origin --delete feature/exampleName
