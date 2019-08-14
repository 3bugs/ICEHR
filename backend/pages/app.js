var post = new Vue({
    el:"#post",
    data:{
        title:'ส่งโปรเจ้ค',
        message:'รายละเอียดโปรเจ้ค'
    }
})

var comment = new Vue({
    el:"#comment",
    data:{        
        text:'อากาศดีมาก'
    }
})

var test = new Vue({
    el: "#test"
})

new Vue({
    el:"#vue-app",
    data:{
        name:"Kong Ruksiam",
        job:"",
        age:20,
        status:false,
        count:0,
        foods:[],
        gender:'',
        website:'http://www.youtube.com/kongruksiamtutorial',
        like: false,
        product:['ผงซักฟอก','แปรงสีฟัน','สบู่','ผ้าขนหนู','กางเกง','เสื้อ'],
        employee:[
            {name:'พงษ์ศักดิ์',age:25,status:false},
            {name:'สมพงษ์',age:30,status:true}
        ],
        newData:{
            empName:'',
            salary:0
        },
        empGroup: [
            {
            empName: 'สมพร',
            salary: 5000
            },
            {
                empName: 'สมศรี',
                salary: 4000
            }
        ]
    },
    methods:{
        getName:function(){
            return this.name
        },
        setName:function(n){
            return this.name=n
        },
        addAge:function(){
            this.age++;
            console.log(this.age);
        },
        SubtractAge:function(){
            this.age--;
            console.log(this.age);
        },
        addCount:function(){
            this.count++;
            console.log(this.count);
        },
        addEmp:function(){
            this.empGroup.push({
                empName: this.newData.empName,
                salary: this.newData.salary
            });
            this.newData.empName = '';
            this.newData.salary = 0;
        },
        showMessage:function(){
           // console.log("บันทึกข้อมูลเรียบร้อย");
           alert("complete");
        }
    },
    computed:{
                //แยกมาจัดการด้านการคำนวณ
                summation:function(){
                    var sum = this.empGroup.reduce(function(value,data){
                        return value + Number(data.salary);
                    },0);
                    return sum;
                },
                avg:function(){
                    var sum = this.empGroup.reduce(function(value,data){
                    return value + Number(data.salary);
                },0);
                return sum / this.empGroup.length;
            }
        },watch: {
            summation: function(){
                this.showMessage();
        }
    }
})

var likeSystem = new Vue({
    el:"#likeSystem",
    data:{
        like:true
    },
    methods:{
        chLike:function(){
            alert('test');
        }
    }
})
