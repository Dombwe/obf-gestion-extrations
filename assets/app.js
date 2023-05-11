/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';





// import friendsofsymfony jsrouting
let Routing = require('../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router');

let Routes = require('../assets/js/js_routes.json');

Routing.setRoutingData(Routes);

let url = Routing.generate('dashboard');


// console.log('Test WebPack');
// console.log(url)