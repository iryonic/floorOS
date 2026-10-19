import { createIcons, icons } from 'lucide';
import Chart from 'chart.js/auto';
import './ui-components.js';

// Initialize Lucide icons on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    createIcons({ icons });
});

window.createFloorIcons = () => {
    createIcons({ icons });
};

window.Chart = Chart;
