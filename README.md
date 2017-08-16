# google-maps-3d-client

[![Join the chat at https://gitter.im/wafflestealer654/google-maps-3d-client](https://badges.gitter.im/wafflestealer654/google-maps-3d-client.svg)](https://gitter.im/wafflestealer654/google-maps-3d-client?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

The smallest possible Google Maps 3d Client

Google created a 3d model of many major cities. Unforutantely, they have obfsucated the JavaScript code responsible for fetching and rendering those 3d models. This repository will contain the scripts needed to create the smallest possible web application with the following goals.

## Goals
 * Reduce HTML, JavaScript, and CSS to the minimum possible working code while maintaing the features describe below.
 * Interpert the meaning of every function. (See Wiki for interperations)

## Minimum Feature Set of Web Application
 * Display satellite imagery
 * Render 3d terrain in every part of the world
 * Render 3d models whenever provided
  * 3d models include trees, buildings, bridges, and anything protuding from the terrain.
 * Allow input devices to control the camera
  * **Zoom**: Scroll wheel to zoom in and zoom out.
  * **Pan**: Click and move cursor to move map.
  * **Rotate**: Press right-control and move cursor to rotate camera.

## Examples of stuff to remove
 * Search Box
 * Directions
 * Social Media
 * Menus
 * CSS (even the JavaScript-injected CSS), Static Images, Icons, Animations
 * Anything deemed fancy that isn't explicitly or implicitly in the minimum feature set.
 * Unnecessary HTML (even the injected HTML)

## Observed Restrictions (if possible, these can be unrestricted)
 * The zoom level will not go further than street level
 * Camera will not go inside of 3d models

## Steps
 * Step 1 (done)
  - Reduce HTML document to the smallest piece of code that will allow the application to continue working.
 * Step 2 (partially done)
  - Separate JS file into definitions and executable code.
  - Remove functions that have no connection with the features mentioned above.
 * Step 3
  - Interpert the meaning of the functions

## Contribute
*TODO: Write a strict contribution document.*
