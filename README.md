Metal Toad Behat Contexts
============

A collection of Behat Context objects to be loaded dynamically 
as subcontexts via tagging your scenarios.

As an example:

@email
Scenario new users get emails
  Given I have an empty inbox
  And I am not a user
  When I create a user account
  Then I should have 1 new message
  And that message should have subject like "Welcome"
  And that message should be from "mysite.com"
  
These steps will be provided by the EmailContext, which is
dynamically bound to the context in the pre-scenario hook.
(i.e. test suites which do not require this functionality won't load it)

#requirements
This functionality requires a (for the time) forked version of behat.
https://github.com/ctrahey/Behat

This can be setup in your composer.json file.