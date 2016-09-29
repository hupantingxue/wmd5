# wmd5
32位：
mmd5   db = conn.mmd5
md32     db = conn.md5_32
mmmd5    db = conn.mmmd5
ntlm    db = conn.ntlm
msha1    db = conn.msha1
    
    
16位    
md16    db = conn.md5_16
sqlmy    db = conn.sqlmy
mmd5_16    db = conn.mmd5_16
    

40位    
 "sha1"    db = conn.sha1              
sqlmy5    db = conn.sqlmy5
sha1md5    db = conn.sha1md5
ssha1    db = conn.ssha1
    
    
64位    
sha256    db = conn.sha256
    

96位    
sha384    db = conn.sha384
    

128位    
sha512    db = conn.sha512
    
0， 创建数据库；
   手动创建；
   wdb32
   wdb16
   wdb40
   wdb64
   wdb96
   wdb128

一， 创建集合；  OK
  1) 生成三个字符连续的集合名称；
  2) 对于以数字开头的集合名称，则在最前面添加s，也就是对于数字打头的长度是四个字符；
  3) 对于python关键字，也需要在前面添加s；

二， 创建索引；  OK
  1) 对于不同的db，其索引不一样；

三， 创建数据；    

1. 选择对应的库；
2. 根据密文前三位选择对应的集合；  

