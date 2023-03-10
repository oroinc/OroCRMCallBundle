@regression
@ticket-BB-22214
@fixture-OroCallBundle:ExtraUser.yml
@fixture-OroCallBundle:CallFixture.yml

Feature: Recent Calls Widget
  In order to have all functions in recent calls widget working well
  as an Administrator
  I should be able to add recent calls widget on dashboard and to sort with exact responses

  Scenario: Add Recent Calls widget
    Given I login as administrator
    When I am on dashboard
    Then I should see "Recent Calls" widget on dashboard
    And I should see following "Recent Calls Grid" grid:
      | SUBJECT     | PHONE NUMBER |
      | Test Call   | 1            |
      | Test Call 3 | 3            |
      | Test Call 2 | 2            |

  Scenario: Set owner in widget configuration
    When I click "Recent Calls Actions"
    And I click "Configure" in "Recent Calls" widget
    And I fill form with:
      | Owner       | Current User |
    And I click "Widget Save Button"
    Then I should see "Widget has been successfully configured" flash message
    And I should see following "Recent Calls Grid" grid:
      | SUBJECT     | PHONE NUMBER |
      | Test Call   | 1            |
      | Test Call 2 | 2            |
    And I should not see "Test Call 3"

  Scenario: Sorting grid by call date
    When I sort grid by "Call Date"
    And I should see following "Recent Calls Grid" grid:
      | SUBJECT     | PHONE NUMBER |
      | Test Call 2 | 2            |
      | Test Call   | 1            |
    And I should not see "Test Call 3"
    When I sort grid by "Call Date"
    And I should see following "Recent Calls Grid" grid:
      | SUBJECT     | PHONE NUMBER |
      | Test Call   | 1            |
      | Test Call 2 | 2            |
    When I click "Recent Calls Actions"
    And I click "Configure" in "Recent Calls" widget
    And I clear "Owner" field
    And I click "Widget Save Button"
    Then I should see "Widget has been successfully configured" flash message
    And I should see following "Recent Calls Grid" grid:
      | SUBJECT     | PHONE NUMBER |
      | Test Call   | 1            |
      | Test Call 3 | 3            |
      | Test Call 2 | 2            |
