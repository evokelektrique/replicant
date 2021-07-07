// const PLUGIN_ICON = require("icon_16x16.png")

let Tabs = {
   bind() {
      [...document.querySelectorAll('[replicant-tab]')].forEach(element => {
         element.addEventListener('click', Tabs.change, false);
      })
   },

   clear() {
      [...document.querySelectorAll('[replicant-tab]')].forEach(element => {
         element.classList.remove('active');
         const id = element.getAttribute('replicant-tab');
         document.getElementById(id).classList.remove('active');
      })
   },

   change(e) {
      Tabs.clear();
      e.target.classList.add('active');
      const id = e.currentTarget.getAttribute('replicant-tab');
      document.getElementById(id).classList.add('active');
   }
}

window.onload = () => {
   Tabs.bind()
   
   // document.getElementById("panel_settings").addEventListener("submit", form => {
   //    form.preventDefault();
   //    console.log(...new FormData(form.target));
   // })
}
