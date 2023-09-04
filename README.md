# Pickleball Tournament Management System 

This is a Laravel project designed to help you create and manage pickleball tournaments. Our project is still in development, and below you'll find more information about the project and usage instructions.

## Project Description
The purpose of this project is to facilitate the process of creating and managing pickleball tournaments. It includes the following features:

- Creating pickleball tournaments
- A "queue-based system" for all current matches and courts
- A timer that assigns the next match to the next available court to finish the tournament within a specified time

## Requirements
Make sure you have the following requirements installed:

- PHP 8.2.9
- Laravel Framework 9.52.15
- MySQL Server version: 8.0.34
- Composer 2.5.8

## Installation
1. Run the migrations to set up the database schema.
2. Set up and configure the crontab for background job processing.

## Usage
1. Create a tournament from the tournament page.
2. Start the tournament, which will generate a list of matches.
3. Start the matches and initiate match tracking using a job.
