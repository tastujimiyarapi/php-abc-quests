                <div class="text-center">
				    <nav>
						<ul class="pagination">
<?php if($pgntn_page != 1 ){ ?>
							<li>
								<a href="<?php echo "list.php?page=".($pgntn_page - 1)."&csrf_key=".$pgntn_csrfKey; ?>" aria-label="前のページへ">
<?php } else { ?>
							<li class="disabled">
								<a href="#" aria-label="前のページへ">
<?php } ?>
									<span aria-hidden="true">«</span>
								</a>
							</li>
<?php for($a=1;$a <= ceil($pgntn_cnt/$pgntn_limit);$a++){ ?>
        <?php if($a == $pgntn_page ){ ?>
							<li class="active">
		<?php } else { ?>
							<li>
		<?php } ?>
							    <a href="<?php echo "list.php?page=".$a."&csrf_key=".$pgntn_csrfKey; ?>"><?php echo $a; ?></a>
							</li>
<?php } ?>
<?php if($pgntn_page != ceil($pgntn_cnt/$pgntn_limit) ){ ?>
							<li>
								<a href="<?php echo "list.php?page=".($pgntn_page + 1)."&csrf_key=".$pgntn_csrfKey; ?>" aria-label="次のページへ">
<?php } else { ?>
							<li class="disabled">
								<a href="#" aria-label="次のページへ">
<?php } ?>
									<span aria-hidden="true">»</span>
								</a>
							</li>
						</ul>
					</nav>
				</div>