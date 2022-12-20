Feature: Call logging
  In order to keep track of all calls made during my daily work
  As a Sales Manager
  I want to be able to keep a record of the outgoing and incoming calls by CRUD them

  Scenario: Logging outgoing call
    Given I login as administrator
    And I go to Activities/Calls
    And I click "Log call"
    And fill "Log Call Form" with:
      | Subject             | Call to Jennyfer                                                              |
      | Additional comments | Offered $40 discount on her next purchase, valid November 2016 - January 2017 |
      | Call date & time    | <DateTime:2016-10-31 08:00:00>                                                |
      | Phone number        | 0501468825                                                                    |
      | Direction           | Outgoing                                                                      |
      | Duration            | 60s                                                                           |
    When I save and close form
    Then I should be on Call View page
    And I should see Call with:
      | Subject             | Call to Jennyfer                                                              |
      | Additional comments | Offered $40 discount on her next purchase, valid November 2016 - January 2017 |
      | Call date & time    | Oct 31, 2016                                                                  |
      | Call date & time    | 8:00 AM                                                                       |
      | Phone number        | 0501468825                                                                    |
      | Direction           | Outgoing                                                                      |
      | Duration            | 00:01:00                                                                      |

  Scenario: Editing call
    Given I click "Edit"
    And fill "Log Call Form" with:
      | Subject             | Call to Jennyfer NB                                                             |
      | Additional comments | Offered $100 discount on her next purchase, valid November 2016 - February 2017 |
      | Call date & time    | <DateTime:2016-10-29 09:30:00>                                                  |
      | Phone number        | 0501468826                                                                      |
      | Direction           | Outgoing                                                                        |
      | Duration            | 59s                                                                             |
    When I save and close form
    Then I should see Call with:
      | Subject             | Call to Jennyfer NB                                                             |
      | Additional comments | Offered $100 discount on her next purchase, valid November 2016 - February 2017 |
      | Call date & time    | Oct 29, 2016                                                                    |
      | Call date & time    | 9:30 AM                                                                         |
      | Phone number        | 0501468826                                                                      |
      | Direction           | Outgoing                                                                        |
      | Duration            | 00:00:59                                                                        |

  Scenario: Viewing call from grid
    Given I go to Activities/ Calls
    When I click view Call to Jennyfer in grid
    Then I should be on Call View page

  Scenario: Editing call from grid
    Given I go to Activities/ Calls
    When I click edit Call to Jennyfer in grid
    Then I should be on Call Edit page
    And I click "Cancel"

  Scenario: Deleting call from grid
    Given there is following Call:
      | Subject         | Direction      | CallStatus        | PhoneNumber |
      | Call to Charlie | @call_outgoing | @call_in_progress | 0501468826  |
    And I reload the page
    And there are two records in grid
    When I click delete Call to Charlie in grid
    And I confirm deletion
    Then there is one record in grid

  Scenario: Deleting call using "Delete" button on call view
    Given I click view Call to Jennyfer in grid
    When I click "Delete"
    And I confirm deletion
    Then there is no records in grid

