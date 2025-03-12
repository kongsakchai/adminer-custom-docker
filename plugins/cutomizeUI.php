<?php
class CustomizeUI
{
    /** @access protected */
	var $servers;
	
	/** 
	 * Set supported servers
	 * @param array $servers
	 * Follow steps from config.md
	 */
	function __construct($servers) {
		$this->servers = $servers;
	}

    function name()
    {
        return '<a href="./" id="h1">Adminer</a>';
    }

    function databases($flush = true) {
		return get_databases($flush);
	}

    function tablesPrint($tables)
    { ?>
<div id='tables'>
    <input id="filter-field" type="search">
    <ul id="tables-list">
    </ul>

    <script<?php echo nonce(); ?>>
        function filterList() {
            console.log('filterList');
            var liTemp = document.createElement('li');
            var aTemp = document.createElement('a');
            var spanTemp = document.createElement('span');
            var filter = document.getElementById("filter-field");
            var ul = document.getElementById("tables-list");

            var tableList = [<?php foreach($tables as $table => $type) { echo "'".urlencode($table) ."'". ",";}?>];

            function appendTables(items) {
                ul.innerHTML = '';
                items.forEach(function (item) {
                    var li = liTemp.cloneNode();
                    var aStruct = aTemp.cloneNode();
                    aStruct.href = "<?php echo h(ME) . 'table=' ?>" + item;
                    aStruct.classList.add('table-structure');

                    var aData = aTemp.cloneNode();
                    aData.href = "<?php echo h(ME) . 'select=' ?>" + item;
                    aData.classList.add('table-name');

                    var span = spanTemp.cloneNode();
                    span.textContent = item;
                    span.title = item;
                    aData.appendChild(span);
                    
                    li.appendChild(aStruct);
                    li.appendChild(aData);
                    li.classList.add('table-item');
                    ul.appendChild(li);
                });
            }

            filter.oninput = function () {
                var value = filter.value.toLowerCase();
                if (!value) {
                    appendTables(tableList);
                } else {
                    var items = tableList.filter(function (item) {
                        return item.toLowerCase().indexOf(value) !== -1;
                    });
                    appendTables(items);
                }
            }

            appendTables(tableList);
        }

        window.onload=filterList;
    </script>
</div>
<?php return true;}

	function loginForm(){
		$drivers = [
            "server" => "MySQL",
            "sqlite" => "SQLite 3",
            "sqlite2" => "SQLite 2",
            "pgsql" => "PostgreSQL",
            "oracle" => "Oracle",
            "mssql" => "MS SQL",
            "mongo" => "MongoDB",
            "elastic" => "Elasticsearch",
        ];
		?>

        <form></form>
		<form id="login-form" action="" method="post" >
			<table cellspacing='0' class='layout'>
				<tr>
					<th><?php echo lang('System') ?></th>
					<td><?php echo html_select("auth[driver]", $drivers, DRIVER, "loginDriver(this);") ?></td>
				</tr>
				<tr>
					<th><?php echo lang('Server') ?></th>
					<td><input name="auth[server]" value="<?php echo h(SERVER)=="" ? "localhost" : h(SERVER) ?>" title="hostname[:port]" placeholder="localhost" autocapitalize="off"></td>
				</tr>
				<tr>
					<th><?php echo lang('Username') ?></th>
					<td><input name="auth[username]" id="username" value="<?php echo h($_GET["username"]) ?>" autocomplete="username" autocapitalize="off"><?php echo script	("focus(qs('#username')); qs('#username').form['auth[driver]'].onchange();") ?></td>
				</tr>
				<tr>
					<th><?php echo lang('Password') ?></th>
					<td><input type="password" name="auth[password]" autocomplete="current-password"></td>
				</tr>
				<tr>
					<th><?php echo lang('Database') ?></th>
					<td><input name="auth[db]" value="<?php echo h($_GET["db"]) ?>" autocapitalize="off"></td>
				</tr>
			</table>
			<section style="display:flex;align-items:center;">
				<p><input type='submit' value='<?php echo lang('Login') ?>'></p>
				<?php echo checkbox("auth[permanent]", 1, $_COOKIE["adminer_permanent"], lang('Permanent login')) ?>
			</section>
		</form>

		<br/>

		<h3><?php echo lang('Server') ?></h3>

		<table cellspacing='0' class='layout'>
			<thead>
				<tr>
					<th><?php echo lang('Name') ?></th>
					<th><?php echo lang('Server') ?></th>
					<th><?php echo lang('Username') ?></th>
					<th><?php echo lang('Database') ?></th>
					<th>Connect</th>
				</tr>
			</thead>

			<tbody>
			<?php
			foreach($this->servers as $name => $server):
				$driver = $server['driver'];
				$host = $server['host'];
				$username = $server['username'];
				$pass = $server['pass'];
				$database = $server['db'];
				?>
				<tr style="text-align:left;">
					<td><?php echo $name ?></td>
					<td><?php echo "($drivers[$driver]) " . $host ?></td>
					<td><?php echo $username ?></td>
					<td><?php echo $databas  ?></td>
					<td>
						<form action="" method="post">
                            <input type="hidden" name="server_name" value="<?php echo $driver; ?>">
							<input type="hidden" name="auth[driver]" value="<?php echo $driver; ?>">
							<input type="hidden" name="auth[server]" value="<?php echo $host; ?>">
							<input type="hidden" name="auth[username]" value="<?php echo $username; ?>">
							<input type="hidden" name="auth[password]" value="<?php echo $pass; ?>">
							<input type='hidden' name="auth[db]" value="<?php echo $database; ?>"/>
							<input type='hidden' name="auth[permanent]" value="1"/>
							<input type="submit" value="<?php echo lang('Connect'); ?>" style="margin:0;"/>
						</form>
					</td>
				</tr>
			<?php
			endforeach;
			?>
			</tbody>
		</table>

		<script <?php echo nonce(); ?> >
			const form = qs('#loginForm')
			form.addEventListener('submit', function(event) {
				const serverName = qs('input[name="auth[server]"]');
				const name = serverName.value;
				if (name.includes('localhost')) {
					serverName.value = name.replace('localhost', 'host.docker.internal');
				}

				return true;
			})
		</script>

		<?php
		return true;
	}

	function loginFormField($name, $heading, $value) {
		return $heading . $value;
	}
}

return new CustomizeUI([]);

?>

