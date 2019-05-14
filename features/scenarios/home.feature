@web
@web_home

Feature: I need to be access to homepage & list trick

  Scenario: Access homepage
    When I go to "/"
    Then the response status code should be 200
