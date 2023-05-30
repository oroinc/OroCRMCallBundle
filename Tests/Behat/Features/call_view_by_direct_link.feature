@ticket-CRM-9408
@fixture-OroCallBundle:CallFixture.yml

Feature: Call view by direct link
  In order to keep system ACL protected
  As an Administrator
  I should be sure that access to the calls by direct links are ACL protected

  Scenario: Feature Background
    Given sessions active:
      | Admin  |first_session  |
      | Admin1 |second_session |

  Scenario: View call with default permissions
    Given I proceed as the Admin
    And I login as administrator
    When I go to Activities/ Calls
    Then I should see following grid:
      | Subject   |
      | Test Call |
    When I click view "Test Call" in grid
    And I should see "Test Call"

  Scenario: Edit view permissions for Call entity
    Given I proceed as the Admin1
    And I login as administrator
    And I go to System / User Management / Roles
    And I filter Label as is equal to "Administrator"
    When I click edit "Administrator" in grid
    And select following permissions:
      | Call | View:None |
    And save and close form
    Then I should see "Role saved" flash message

  Scenario: View call by direct link without view permission
    Given I proceed as the Admin
    When I reload the page
    Then I should see "403. Forbidden You don't have permission to access this page."
