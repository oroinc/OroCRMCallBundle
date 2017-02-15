@fixture-case_crud.yml
Feature: Activity cases
  In order to keep track of all trouble cases
  As a Sales Manager
  I want to be able to keep a story of activity cases by CRUD them

  Scenario: Create case
    Given I login as administrator
    Then I go to Activities/ Cases
    And I press "Create Case"
    When I save and close form
    Then I should see validation errors:
      | Subject         | This value should not be blank.  |
    And I fill form with:
      | Subject         | Test case subject      |
      | Description     | Case for behat testing |
      | Resolution      | Create through form and check |
      | Assigned To     | John Doe               |
      | Source          | Web                    |
      | Status          | In Progress            |
      | Priority        | High                   |
      | Related Contact | Charlie Sheen          |
      | Related Account | Bruce                  |
    When I save and close form
    Then I should see Test case subject in grid with:
      | Subject         | Test case subject      |
      | Description     | Case for behat testing |
      | Resolution      | Create through form and check |
      | Assigned To     | John Doe               |
      | Source          | Web                    |
      | Status          | In Progress            |
      | Priority        | High                   |
      | Related Contact | Charlie Sheen          |
      | Related Account | Bruce                  |
