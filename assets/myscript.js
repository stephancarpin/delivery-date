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

    var date_array = split_dates;






    $('#datepicker_example').multiDatesPicker({
        dateFormat: 'dd-mm-yy',
        addDates: split_dates,
        onSelect:function(event){

            if (date_array.indexOf(event) === -1){


                date_array.push(event);

            } else {

                var index = date_array.indexOf(event);

                date_array.splice(index,1);

            }

            inputDates.value = date_array;

        }
    });








});






