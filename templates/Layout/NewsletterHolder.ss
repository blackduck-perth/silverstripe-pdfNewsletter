<!-- NewsletterHolder Layout -->
<div class="content-container col-sm-10 col-sm-offset-1">
	<article>
		<h1>$Title</h1>
		$Content
		<div class="Newsletters">
			<% loop NewslettersByYear.GroupedBy('IssueYear') %>
				<div id="$IssueYear" class="row Year">
					<div class="col-sm-6 col-md-9 detail">
						<h2>$IssueYear</h2>
						<% loop Children() %>
							<div class="col-xs-3 col-sm-2 col-md-1" ><a href="{$IssuePDF.URL}" target="_NEW">$IssueMonthName</a></div>
						<% end_loop %>
					</div>
				</div>
			<% end_loop %>
		</div>
	</article>
	$Form
</div>
