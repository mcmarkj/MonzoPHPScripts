# MonzoPHPScripts
A series of PHP scripts for better managing my money with Monzo!

Using - Mondo OAuth Framework & Twilio for SMS Comms.

Script Built to round my balance to the nearest £5 and transfer the change into a pot. 

I've also built a email parser using Zapier to understand when my expenses will be paid. Schedule that in a DB and then transfer the payment into another pot on the morning of the transfer.

Although this is all obviously specific to my use case. I hope this could help you do something you want! 

# To Do

- Make a Composer File to gain all the requirements. 
- Massive clean up of code needed!
- Move to MYSQLI to stop warnings
- Provide a SQL template to get the DB setup

# How to use

1. Rename the Config file to 'Config.php'
2. Update the Config file with all of your creds. 
3. Access Index.php to Authenticate with Monzo
4. Monzobalance.php will now start rounding your balance to the nearest £5. 
