var config = {
    "paths": {
        'owlcarousel': "MGS_Portfolio/js/owl.carousel.min"
    },
    "map": {
        "*": {
            "instafeed": "js/instafeed",
            "customjquery": "https://code.jquery.com/jquery-3.3.1.min.js",
            "pagination": "js/pagination",
            "native-loading": "js/ls.native-loading.min"
        }
    },
    "shim": {
        "instafeed": ["jquery"],
        "native-loading": ["jquery"],
        "pagination":{
            'deps':['jquery']
        },
        'owlcarousel': ['jquery']
    },
    "deps": [
        "js/script"
    ]
};