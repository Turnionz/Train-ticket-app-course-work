# Migrations

> ## ~Realize the DB structure with migrations~
>
> > - ~Create models~
> > - ~Define the relations~
> > - ~Create factories~
> > - ~Set up the seeder~
> > - ~Create and run migrations~

# Auth

> ## Set up the access logic for different level of entry
>
> > - ~Login/create/delete account logic~
> > - ~Make admin, operator, employee and customer level of access~
> > - Set up the different UI elements depending on the level of access _(in progress)_

# Creation/Building/Editing

> ## Make the logic for creating/editing different kind of Models
>
> > - ### Ticket creation/editing
> >     > - Add search function for Trips (must show count of free spaces)
> >     > - Selection of seats using checkbox (dynamic pricing)
> >     > - Adding/reusing/editing different passengers for one user
> > - ### Station creation/editing (operator only)
> >     > - General form
> >     > - Assigning neighbouring stations
> > - ### Train and wagon creation/editing (operator only)
> >     > - Creating a train
> >     > - Add abbility to make new wagons just for this train, transfering the user to wagon creation page
> >     > - Wagon creation with dynamic seat allocation (add/remove row/column, set seat type etc.)
> >     > - Should be able to create several wagons at once
> >     > - Upon creation of new wagons, if user was sent to wagon creation page from train creation, send back to creating the train with automatical assigning of newly created wagons
> > - ### Route creation/editing (operator only)
> >     > - No same routes can be made
> >     > - Must list the order of stations in creation
> >     > - Time between stations must be defined as "at average speed of X kms" for further dynamic calculation of train travel time (So is basically a multiplier) OR must be able to just state time of travel at set speed
> > - ### Employee registering/editing (operator only)
> >     > - Only operator should be able to make new employee
> >     > - No operator can create other operator, only admin can
> > - ### Crew and assignment building/editing (operator only)
> >     > - Crews must consist at least from 1 employee
> >     > - Assignments can be empty, so can exist before the trip
> > - ### Trip creation/editing (operator only)
> >     > - Train availabilty error
> >     > - Crew assignment at creation (with availability error)
> >     > - Trips should be able to change Route in RT, with a notification to everyone aboard that trip
> >     > - Trips should be able to have emergency route change to a single station, also with notification to everyone on that trip

# Create tests
