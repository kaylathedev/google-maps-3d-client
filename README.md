# google-maps-3d-client
The smallest possible Google Maps 3d Client

Google created a 3d model of many major cities. Unforutantely, they have obfsucated the JavaScript code responsible for fetching and rendering those 3d models. This repository will contain the scripts needed to create the smallest possible web application with the following goals.

Goals of Web Application
 * View terrain models and imagery
 * View 3d models of all man-made or natural structures (anything that protudes from the surface of the ground)
 * Allow user (or a controller script) to move in the 3d space

Examples of stuff to remove
 * Search, Directions, Social Media, Menus, Icons, Animations, anything deemed fancy, etc.

Notable Restrictions (if possible, these can be unrestricted)
 * The zoom will not zoom further than street level
 * Camera will not go inside of 3d models

Steps
 * Step 1 (done)
  - Reduce HTML document to the smallest piece of code that will allow the application to continue working.
 * Step 2
