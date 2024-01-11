import './bootstrap';
import Alpine from 'alpinejs';
import {Flashes} from './flashes';

window.Alpine = Alpine;
Flashes('flashes');

Alpine.start();
