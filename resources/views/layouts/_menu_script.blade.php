            <!-- Set active menu -->
			<script>
                $(document).ready(function() {
                    for (i=0; i < 10; i++) {
                        $("li[rel"+i+"='{{ $controller }}']").addClass("active");
                    }
                    for (i=0; i < 10; i++) {
                        $("li[rel"+i+"='{{ $controller }}/{{ $action }}']").addClass("active");
                    }                
                });                    
			</script>
			<!-- /set active menu -->