# project mylsu

### what problem is it solving

- Currently Lsu is manually assigning rooms to students the day they arrive on campus, this app makes this whole process automatic.

### What stack is it built on

- Vuejs for frontend
- Laravel for backend

### What you will need

- Vue 3
- Laravel 9

## How to start up everything

### To start the frontend app

- cd into client
- run `npm run serve`
- runs on default localhost port 8080

### Before you start the backend app
- make sure you have a database called mylsu
- go into the server folder, track the environment file, change the db user and password variables

### To start the backend app

- cd into sever
- run `php artisan serve`
- runs on default localhost port 8000

### Before we start

- l use insomnia as my http client, l have included a file in the folder insomnia inside the server that you can import in your http client.

### Important db notes

- the .env db is set on mylsu
- the student table stores all the personal info of the student
- the profile table stores all the school related info
- the users table stores all the login info
- the activeStudentType stores info that determines which students [block/conventional] will be allocated rooms, the default is set to conventional
- the search exception table store programs for students who are part 3's but go for attachment part 4
- the residence table stores info related to which student has been allocated which room
- other tables worth of notice are faculties, tuition, payments, programs etc.

### Okay, let's start doing something

- after migration, you have to seed right?
- the student seeder file by default seeds 1400 students, 750 females and 650 males.
- to test logging in, signing up, resetting the password, in the database seeder file comment out the users seeder.
- to test the actual application and to by pass the login/registration what what stages seed the users file.
- the users seeder file will create a user for each student with a default password of 12345678 for very student
- the profile seeder is of much importance because it determines the spread of students like part 1.2, 2.1, 3.1, whether or not to add block students and stuff, go check it out.
- it's important to note that the student_type is written in full in the profile table only, in others its con and block.

- The AuthController file handles all the logging/registration interactions


### To migrate and seed the database
- run `php artisan migrate`
- then run `php artisan seed`

### How test logging/registration/resetting passwords

- To do these you will need a student id, national ID and a DOB, these are all available in the students table.
- To quicken the process l have an http request named random student, that will give you a fresh student id, the national id and the dob.

### How everything actually works

- after the student has logged in, there are given a search area to allow them to search for their preferred roommates, 3 at maxinum, then they make a request. The selected roommates will be given an option to decline or accept the pairing of roommates when they do log in.

- The Home controller file handles the user interface when they login.
- The search controller file handles all the activities of using the search area

- That request is sent to the backend and recorded in two essential tables, the requester table and the request_candidates table.
- The requester table records the student who selected other students to be room mates and also a marker that determines if the request has been processed that is by the VEngine.
- The request_candidates table records the selected roommates and their responses.
- The file that handles all these interactions is the RequestController

### Some vocab

requester - the student who selected other students to be roommates with them
selected roommate - this being the student who was picked by the requester as a preferred roommate.

### About the activeStudentType thingy

- Universities have two types of students, the conventional ones and block and they both need rooms, so this file sets which students do the below mentioned engine work with, by default it is to conventional.

### The VEngine

- This is the part of the code that actually does the allocation of rooms, it allocates the rooms based on the number of responses each requester has from his roommates, the expected responses are three yesses, if it is short it will search through other requesters and add to make three.
- This file is found in the controllers folder

### The VAudit

- This is the code that makes sures if every student has been given a room, it works with the requests table.
- This file is found in the traits folder

### How to test the VEngine and the VAudit

- Reset all migrations
- Then seed ALL files.
- You will need to generate some fake room requests, the http client file includes a request called generate requests, hit on that request. Generating requests might be slow and might cause a server timeout, though l hv increased the the server timeout, it might not happen, however if does happen, run the request again.

- After you have successfully generated the fake requests, you can check for changes in the requesters request_candidates and requests tables

- the table requests records every student that has requested a room or has confirmed a roommate selection,
  however the total number of requests won't match the total number of students because some students are block, some are under commercials and some are part 3's hence they are automatically excluded when we generate fake requests, and every student that has declined a roommmate selection and has no request of his own is also exlcuded

- Now everything is set, you can now run the http client request VEngine. This client also run VAudit.
- Upon its completion you can check the residence table and rooms table for changes
- After than you can get a random student id using the random student http request and login using the default password 12345678.

### Some cheat codes

- if you want to start afresh in terms of generating requests, you can hit the delete all requests and the clear rooms http requests and you good to go

### Main files

- AuthController
- HomeController
- RequestsController
- GenerateRequests
- VEngine
- VAudit
- ProgramsMall
