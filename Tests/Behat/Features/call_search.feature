@fixture-OroUserBundle:user.yml
@fixture-OroCallBundle:CallFixture.yml

Feature: Call entity search
  In order to search Call
  As an user
  I should see view page of Call entity in search results with role permissions 'View:Global' for Call entity

  Scenario: Edit view permissions for Call entity with Sales Rep Role
    Given I login as administrator
    Then go to System / User Management / Roles
    When I filter Label as is equal to "Sales Rep"
    And I click edit "Sales Rep" in grid
    And select following permissions:
      | Call | View:Global |
    And save and close form
    Then I should see "Role saved" flash message

  Scenario: Search Call
    Given I login as "charlie" user
    And I click "Search"
    And type "Test Call" in "search"
    When I click "Search Submit"
    Then I should be on Search Result page
    And I should see following search entity types:
      | Type           | N | isSelected |
      | Calls          | 1 |            |
    And I should see following search results:
      | Title     | Type |
      | Test Call 1 | Call |

  Scenario: View entity from search results
    Given I follow "Test Call"
    Then I should be on Call View page
