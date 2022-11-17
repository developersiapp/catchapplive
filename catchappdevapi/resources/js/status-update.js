import Echo from "laravel-echo"

window.io = require('socket.io-client');

// Have this in case you stop running your laravel echo server
if (typeof io !== 'undefined') {
    window.Echo = new Echo({
        broadcaster: 'socket.io',
        host: window.location.hostname + ':6002',
    });

    window.Echo.channel('socket_demo_database_admin-notification').listen('AdminNotification', (e) => {
        console.log(e);
        $('#timestamp-table').append('<h1>' + e.update + '</h1>')
    });

    // var timeStamps = [];
    // function updateTimeStampTable()
    // {
    //     var tableHtml = '<table><tbody>';
    //     timeStamps.forEach(function (val) {
    //         tableHtml += '<tr><td>' + val + '</td></tr>';
    //     });
    //     tableHtml += '</tbody></table>';
    //     $('#timestamp-table').html(tableHtml);
    // }
    // window.Echo.channel('socket_demo_database_admin-notification').listen('AdminNotification', (e) => {
    //     console.log(e);
    //     timeStamps.push(e.update);
    //     updateTimeStampTable();
    // });
}

// Echo.join('admin-notification')
//     .here((users) => {
//         this.users_viewing = users;
//         this.$forceUpdate();
//     })
//     .joining((user) => {
//         if (this.checkIfUserAlreadyViewingSurvey(user)) {
//             this.users_viewing.push(user);
//             this.$forceUpdate();
//         }
//     })
//     .leaving((user) => {
//         this.removeViewingUser(user);
//         this.$forceUpdate();
//     });
