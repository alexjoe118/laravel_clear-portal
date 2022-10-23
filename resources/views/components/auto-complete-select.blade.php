
@php
	$searchword = $searchword ?? '';
@endphp
<script type="text/javascript">

    $("#search").keypress(function(e) {
        //to call ajax
        //remoteSearch();
        //or static search
        var v = this.value.replace(/\s+/g, " ").trim().toLowerCase();
        if (v == "") return $(".option").hide();
        $(".option").hide();
        $(".option").each(function() {
            var t = $(this).text().toLowerCase();
            if (t.indexOf(v) > -1) $(this).show();
        });
    });

    $(document).on("click", ".option", function() {
        $("#search").val($(this).text());
        $(".option").hide();
    });
	function changename(){
		if($("#search").val() == ""){
			window.location.href='/dashboard/admin/user';	
		}
		var v = $("#search").val().replace(/\s+/g, " ").trim().toLowerCase();
        if (v == "") return $(".option").hide();
        $(".option").hide();
        $(".option").each(function() {
            var t = $(this).text().toLowerCase();
            if (t.indexOf(v) > -1) $(this).show();
        });
		
	}
	function getUser(user_id){
		const obj = document.getElementById("changepage");
		window.location.href='/dashboard/admin/user?userid='+user_id+'&searchword='+$('#search').val()+'&pagecount='+obj.value;
	}
    function remoteSearch() {
        $.ajax({
            url: "/dashboard/admin/user/getname",
            data: {
                "search": $("#search").val() //search box value
            },
            dataType: "json", // recommended response type
            success: function(data) {
                //data = ["name1","name2","name3"];
                $(".options").html(""); //remove list
                $.each(data, function(i, v) {
                    $(".options").append("<li class='option'>" + v + "</li>");
                });
            },
            error: function() {
                alert("can't connect to db");
            }
        });
    }
</script>
<style>
    #search {
        padding: 5px;
    }

    #select-containe {
        width: 200px;
    }

    .option {
        padding: 5px;
        display: none;
        color: white;
        background: #104378;
        cursor: hand;
    }

    .option:hover {
        color: #104378;
        background: white;
    }

    .options {
        height: 300px;
        width: 250px;
        overflow: auto;
        padding: 0;
        margin-top: 360px;
        position: absolute;
		z-index: 100;
    }
</style>

<fieldset class="form-field field-basic">
    <div class="input-wrapper">

		<div id='select-container' class="input-basic">
			<input id='search' placeholder='search names' onkeyup="changename()" value="{{$searchword}}"/>
			<ul class='options'>
				@foreach ($all_users as $user)
					<li class='option' onclick="getUser({{$user->id}})">{{getTitle($user)}}</li>
				@endforeach

			</ul>
		</div>
    </div>
</fieldset>
