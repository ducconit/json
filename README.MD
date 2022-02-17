## Json manager
date: 01:28 18/02/2022
coded by Ducnt <ducconit@gmail.com>

### Cách dùng
 #### 1. cài đặt
```composer require dnt/json```

#### 2. khởi tạo
```php
use DNT\Json\Json;

$json = Json::make(string $path, bool $ensure = false, int $mode = 0755, $recursive = true);
// hoặc
$json = new Json(string $path, bool $ensure = false, int $mode = 0755, $recursive = true);

```
- $path: đường dẫn đến file. Một đường dẫn hợp lệ là 1 đường dẫn đến 1 file có quyền đọc và ghi file
- $ensure: nếu `true` : file không tồn tại thì sẽ khởi tạo. Nếu `false` file không tồn tại sẽ ném ra exception `FileNotFoundException`
- $mode: quyền truy cập
- $recursive: nếu đường dẫn là xâu của nhiều thư mục thì có cho phép tạo các thư mục đó không

*Khi khởi tạo thì ngay lập tức sẽ tìm đến `$path` để đọc nó và `convert` qua `array`*

### 3. các hàm bổ trợ
```php 

    // đặt đường dẫn mới
	public function setPath(string $path): Jsonable;

    // lấy đường dẫn hiện hành
	public function getPath(): string;

    // lấy thuộc tính - nếu null hoặc không tồn tại thì sẽ lấy $default - $default có thể là 1 callable
	public function get(string $key, mixed $default = null): mixed;
    
    // chuyển sang dạng json
	public function toJson(int $flag = JSON_FORCE_OBJECT): string;

    // kiểm tra thuộc tính tồn tại không
	public function has($offset): bool;
    
    // đặt giữ liệu mới
	public function setAttributes(array $attributes = []): Jsonable;
    
    // lấy tất cả thuộc tính
	public function all(): array;
	
	// lưu các thay đổi vào file
	public function save(): bool;
```

* Ngoài ra có thể sử dụng:
```php 
use DNT\Json\Json;

$json=Json::make($path);

// Lấy thuộc tính tương tự $json->get('foo');
$json->foo;
 
 // đặt giá trị mới tương tự $json->set('foo','bar');
 $json->foo = 'bar';
 
 // sử dụng anonymous function
 $json->foo = function() {
    return 'bar';
 };
 // hoặc
 $json->set('foo',function(){
    return 'bar';
 });
 // cũng có thể dùng callback để lấy giá trị mặc định
 $json->get('foo',function(){
    return 'bar';
 });
```

### 4. test
```composer test```
