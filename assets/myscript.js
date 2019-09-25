window.addEventListener("load",function()
{


     var tabs = document.querySelectorAll("ul.nav-tabs > li");

     for (var i = 0; i< tabs.length; i++)
     {
         tabs[i].addEventListener("click",switchTab);
     }

     function switchTab(event)
     {
         event.preventDefault();
         document.querySelector("ul.nav-tabs li.active").classList.remove('active');
         document.querySelector(".tab-pane.active").classList.remove('active');

         var clickedTab = event.currentTarget;
         var anchor = event.target;
         var activePaneID = anchor.getAttribute("href");

         clickedTab.classList.add("active");
         document.querySelector(activePaneID).classList.add("active");
     }





    var inputDates= document.getElementById('setDates');
    var split_dates = inputDates.value.split(",");

    $('#datepicker_example').multiDatesPicker({
        dateFormat: 'dd-mm-yy',
        addDates: split_dates,
        onSelect:function(event){
            var initialValues = inputDates.value;

            if (initialValues === '')
            {
                inputDates.value = event;

            } else {

                inputDates.value = initialValues+','+ event ;

            }
        }
    });








});






