import Datepicker from "flowbite-datepicker/Datepicker";
import pt_BR from 'flowbite-datepicker/locales/pt-BR';

Object.assign(Datepicker.locales, pt_BR);

document.addEventListener('DOMContentLoaded', function() {
    const datepickerEl = document.getElementById('date-picker');
    if(datepickerEl) {
        const data_picker = new Datepicker(datepickerEl, {
            format: 'dd/mm/yyyy',
            language: 'pt-BR',
            clearBtn: true
        });
    }
});

