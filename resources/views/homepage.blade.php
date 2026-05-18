<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/logo-tooth.png') }}" type="image/x-icon">
    <title>EP CLINIC</title>

    <meta property="og:title" content="Espineli-Paradeza Dental Clinic">
    <meta property="og:description" content="Your Smile, Our Expertise bringing confidence, comfort and care to every visit.">
    <meta property="og:image" content="/images/pic4.jpg">
    <meta property="og:url" content="https://epclinic.cloud">

    @vite('resources/css/app.css')
    {{-- @vite('resources/js/lightDark.js') --}}
    <style>
        *{
            font-family: "Poppins", sans-serif;
            scroll-behavior: smooth;
        }
        body {
  overflow-x: hidden;
}
        .image-wrapper {
      display: flex;
      justify-content: center;
      padding-top: 5rem;
      align-items: center;
    }
    .containerr{
        max-width: 768px;
        margin: auto;
    }
    
    .inputss {
            border: none;
            padding: 1rem;
            border-radius: 1rem;
            box-shadow: 20px 20px 60px #c5c5c5,
                    -20px -20px 60px #ffffff;
            transition: 0.3s;
        
            }

            .inputss:focus {
            outline-color: #e8e8e8;
            box-shadow: inset 20px 20px 60px #c5c5c5,
                    inset -20px -20px 60px #ffffff;
            transition: 0.3s;
            }

    .image-wrapper::before {
      content: "";
      position: absolute;
      background: rgba(80, 80, 80, 0.503);
      width: 25rem;
      height: 50px;
      bottom: 100px;
      filter: blur(20px);
      border-radius: 50%;
    }

    @media(max-width:768px){
      .image-wrapper::before {
          content: none; /* This will effectively hide the pseudo-element */
      }

      .image-wrapper{
        display: flex;
        flex-direction: column;
        gap: 16px;
        max-width: 100%;
       }

      .image-wrapper img {
        width: 70% !important;
        height: 300px !important;
        clip-path: none !important;
        margin: 0 0 1rem 0 !important;
        border-radius: 6px;
    }

          .image-wrapper .img1,
          .image-wrapper .img2,
          .image-wrapper .img3 {
              height: auto; /* Ensure heights adjust automatically */
              margin-right: 0; /* Remove any specific right margins */
          }

        
          .containerr{
              width: 100%;
              margin: 0;
              padding: 1rem; /* Adjust container padding as needed */
          }

    }

    .image-wrapper img {
      width: 270px;
      clip-path: polygon(25% 0%, 100% 0%, 75% 100%, 0% 100%);
      transition: 0.5s ease-out 100ms;
      cursor: pointer;
    }
    

    .image-wrapper img:hover, .map:hover {
      transform: scale(1.05);
    }

    .image-wrapper .img1 {
      height: 20rem;
      margin-right: -30px;
    }

    .image-wrapper .img2 {
      height: 25rem;
      margin-right: -30px;
    }

    .image-wrapper .img3 {
      height: 20rem;
    }

    .service-card:hover{
      background-color: #4158D0;
      background-image: linear-gradient(43deg, #4158D0 0%, #C850C0 46%, #FFCC70 100%);
      box-shadow: rgba(0, 0, 0, 0.17) 0px -23px 25px 0px inset, rgba(0, 0, 0, 0.15) 0px -36px 30px 0px inset, 
      rgba(0, 0, 0, 0.1) 0px -79px 40px 0px inset, rgba(0, 0, 0, 0.06) 0px 2px 1px, 
      rgba(0, 0, 0, 0.09) 0px 4px 2px, rgba(0, 0, 0, 0.09) 0px 8px 4px, rgba(0, 0, 0, 0.09) 0px 16px 8px, 
      rgba(0, 0, 0, 0.09) 0px 32px 16px;
      color: white;
      transition: 0.5s ease-in;
      cursor: pointer;
    }
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
     <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
     <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
</head>
<body>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
       AOS.init();
     </script>

    <div class="w-full flex items-center justify-around max-sm:justify-center px-4 py-6" data-theme="light" id="navbar">
        <div class="flex items-center justify-start gap-2"  data-aos="fade-up"
        data-aos-duration="2000">
            <a href="#hero" class="md:text-2xl  text-sm sm:text-md text-[#0118D8] tracking-tighter font-bold"><span class="max-sm:text-center">EP</span> <br class="max-sm:block hidden"> DENTAL-CLINIC</a>
            <img src="{{ asset('images/logo-tooth.png') }}" alt="logo" class="h-[50px] w-[50px] p-2 rounded-full">
        </div>
        <div class="flex items-center gap-2 justify-center text-center" data-aos="fade-up"
        data-aos-duration="2000"> 
                <a href="#about" class="md:text-xl text-md text-black transition-colors rounded-md  px-4 py-3 hover:bg-[#404fd3] hover:text-white">About</a>
                <a href="#services" class="md:text-xl text-md text-black rounded-md px-4 py-3 hover:bg-[#404fd3] hover:text-white transition-colors ">Services</a>
             
        </div>
    </div>


    <section id="hero" class="w-full min-h-[80vh] " data-theme="light">
       
        <div class="flex items-center justify-center gap-4 max-lg:flex-col max-md:gap-7  px-6  md:mx-20 md:m-auto">
            <div class="flex flex-col items-center text-center pt-[5rem] justify-end   " data-aos="fade-up"
                 data-aos-duration="2000">
                <h4 class="font-bold text-[40px] py-2 text-[#0118D8] max-sm:text-center max-sm:px-4 ">Your Smile, Our Expertise</h4>
                <p class="text-xl text-center py-2 text-black">Bringing confidence, comfort and care <br>  to every visit.</p>
                <a href="/appointment/create" class="bg-[#0118D8] hover:bg-[#404fd3] rounded-lg outline-none  text-white px-8 py-3 text-lg mt-4">Schedule an Appointment</a>
            </div>
            <div class="containerr" >
                <div class="image-wrapper">
                  <img
                    class="img1"
                    src="/images/pic1.jpg"
                    alt=""
                  />
                  <img
                    class="img2"
                    src="/images/pic6.jpg"
                    alt=""
                  />
                  <img
                    class="img3"
                      src="/images/pic4.jpg"
                    alt=""
                  />
                </div>
              </div>
        </div>
    </section>

    <section class="w-full h-[65vh] " data-theme="light">
        <div class="flex md:items-start items-center justify-center flex-col gap-6 max-w-[700px] mx-auto pt-40 max-sm:px-4">
            <div class="flex items-start justify-center md:text-start text-center  gap-4" data-aos="fade-left"
            data-aos-duration="2000">
                
                <h4 class="font-bold md:text-[45px] text-4xl text-black">Your Journey to a <span class="text-[#0118D8]">Brighter</span></h4>
            </div>
            <div class="flex items-start justify-center text-center md:justify-end gap-4 w-full"
            data-aos="fade-right"
            data-aos-duration="2000">
                <h4 class="font-bold md:text-5xl text-4xl text-black"> and <span class="text-[#0118D8]">Healthier</span> Smile</h4>
                
            </div>
            <h4 class="flex justify-center items-center text-center w-full pt-8 italic text-xl text-slate-700">Let us help you achieve the smile you’ve always wanted.</h4>
        </div>      
    </section>

   <section class="w-full min-h-screen py-16 bg-gray-50" id="about" data-theme="light">
    <div class="max-w-[1000px] mx-auto px-4">
        <h4 class="text-center font-bold text-4xl text-[#0118D8] mb-12"
            data-aos="fade-up"
            data-aos-duration="1500">About</h4>
        <div class="flex max-md:flex-col items-center justify-center gap-12 lg:gap-20">
            <div class="md:text-start text-center md:max-w-[60%] order-2 md:order-1"
                data-aos="fade-right"
                data-aos-duration="2000">
                <h4 class="lg:text-3xl text-2xl font-bold py-4 text-[#0118D8]">Dr. Ma. Elena Espineli-Paradeza 👋</h4>
                <p class="text-lg text-black text-justify leading-relaxed">"Welcome to EP-Clinic, led by Dr. Espineli-Paradeza, a dedicated dentist committed to
                    providing exceptional dental care with a gentle touch. With a passion for helping patients 
                    achieve a healthy, beautiful smile, Dr. Espineli-Paradeza combines expertise and a patient-centered 
                    approach in a comfortable, modern setting. Whether it’s routine check-ups, preventive care, or specialized treatments, 
                    our clinic is here to support your oral health journey with professionalism and compassion."</p>
            </div>
            <div class="order-1 md:order-2"
                data-aos="fade-left"
                data-aos-duration="2000">
                <img src="{{ asset('images/pic7.jpg') }}" alt="Dr. Ma. Elena Espineli-Paradeza" class="w-64 h-64 md:w-80 md:h-80 mx-auto rounded-full object-cover shadow-xl">
            </div>
        </div>
    </div>
</section>

    <section class="w-full min-h-[60vh] py-16" id="services"  data-theme="light">
        <h4 class="py-8 text-center font-bold text-5xl text-[#0118D8] " >Services</h4>
        <div class="grid lg:grid-cols-3 items-center md:grid-cols-2 grid-cols-1 gap-7 max-w-[1000px] mx-auto px-4 py-10">
            @forelse ($treatments as $item)
            <div class="text-center border-2 border-black rounded-md service-card" data-aos="fade-up"
            data-aos-duration="1500">
                <h4 class=" text-3xl max-md:text-xl text-center font-bold py-3">{{$item->treatment_offer}}</h4>
            </div>
            @empty
                <h4 class="text-2xl pt-10 text-center col-span-3"> No services at the moment.</h4>
            @endforelse
           
        </div>
    </section>

    <section class="w-full min-h-[100vh] py-8 bg-gray-100" id="services"  data-theme="light">
        <h4 class="py-8 text-center font-bold text-5xl text-[#0118D8]" data-aos="fade-up"
        data-aos-duration="1500">Locate Us</h4>
        <div class="flex max-md:flex-col items-center justify-center gap-10 lg:gap-6 py-12 max-w-[1000px] mx-auto">
            <div class="px-6"
            data-aos="fade-up"
            data-aos-duration="1500">
                <iframe class="rounded-lg max-w-full" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1846.1457326622813!2d119.93123849999996!3d15.624494300000011!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33940fb452537e0f%3A0x300cb20d4d43220!2sKABUHAYAN%20NATIN%20STORE!5e1!3m2!1sen!2sph!4v1732175881252!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            <div class="px-4 text-xl md:max-w-[45%] leading-7 text-justify"
            data-aos="fade-up"
            data-aos-duration="1500">
                Locate us near the <span class="text-[#0118D8]">Trillana Cycle Parts Store</span class="text-[#0118D8]"> at the <span class="text-[#0118D8]">Rubi Street Poblacion, Candelaria, Zambales.</span > We are open from <span class="text-[#0118D8]">Monday to Sunday at 8am to 4pm</span>. Make your appointment now and we will give you the brightest smile you will ever have!
            </div>
        </div>
    </section>

     <section class="w-full min-h-[65vh] py-16" data-theme="light">
        <div class="flex items-center justify-center flex-col  gap-6 max-w-[1050px] mx-auto py-20 max-sm:px-4">
            <h4 class=" font-bold text-4xl py-4 text-center pt-8 text-[#0118D8]">Get Personalized Dental Insights from Our AI Assistant</h4>
            <div class="grid gap-6 max-w-[700px] mx-auto items-center text-center p-5 rounded-lg outline-none glasscss">
                <h4 class="py-4 text-3xl text-black">DISCLAIMER:</h4>
               <p class="text-md">This AI assistant is designed to offer preliminary information and is not a substitute for professional dental consultation. 
                The insights and suggestions provided are dependent on your input and may not be entirely accurate.
                 For a comprehensive evaluation and personalized advice, always consult with a qualified dentist.</p>
            </div>
            <form action="{{route('patient.assess')}}" id="dentalForm" class="flex max-lg:flex-col items-around justify-center gap-4 p-4 w-1/2 max-sm:w-full mx-auto" method="POST">
                @csrf
                @method('POST')
                <input
                  type="text"
                  name="dental_condition"
                  placeholder="Enter your dental condition"
                  class="
                    w-full                             
                    bg-white
                    placeholder:text-slate-500
                    p-2                    
                    rounded-md             

                    ring-2          
                    ring-blue-500
                  "
                />
                <button class="bg-blue-600 hover:bg-blue-700 transition-colors px-8 py-3 text-white text-lg  font-bold rounded-lg shadow-xl outline-none">Submit</button>
            </form>
            <div class="rounded-xl shadow-xl mt-8 w-full lg:max-w-[1000px] mx-auto bg-white p-6" id="result">
                <p class="text-gray-500 text-center">Your personalized dental insights will appear here after submission.</p>
            </div>
        </div>   
        
        <script>
        const form = document.getElementById('dentalForm');
        const dentalInput = form.querySelector('input[name="dental_condition"]');
        const resultDiv = document.getElementById('result');

        form.addEventListener('submit', async (event) => {
            event.preventDefault(); // Prevent default form submission (page reload)

            const dentalCondition = dentalInput.value.trim();

            if (!dentalCondition) {
                resultDiv.innerHTML = '<div class="p-4 text-red-600 font-semibold text-center">Please enter your dental condition before submitting.</div>';
                return;
            }

            // Show a loading message
            resultDiv.innerHTML = '<div class="p-4 text-blue-600 text-center font-semibold">Analyzing your symptoms...</div>';

            try {
                // Get the CSRF token from the meta tag
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken // Laravel CSRF protection
                    },
                    body: JSON.stringify({ dental_condition: dentalCondition }) // Send as JSON
                });

                if (!response.ok) {
                    // If response is not OK (e.g., 4xx or 5xx), parse error details
                    const errorResponse = await response.json(); // Try to parse as JSON
                    const errorMessage = errorResponse.message || 'An unexpected error occurred.';
                    throw new Error(`HTTP error! Status: ${response.status}. Details: ${errorMessage}`);
                }

                const data = await response.json(); // Parse the successful JSON response

                if (data.error) {
                    resultDiv.innerHTML = `<div class="p-5 text-red-600 font-semibold">${data.error}</div>`;
                } else {
                    // Display the recommendation from Gemini
                    resultDiv.innerHTML = `
                        <div class="p-5 bg-white rounded-lg text-left border border-gray-200">
                            <h5 class="font-bold text-2xl mb-3 text-[#0118D8]">AI Assistant's Insights:</h5>
                            <p class="text-gray-800 whitespace-pre-wrap leading-relaxed">${data.recommendation}</p>
                        </div>
                    `;
                }

            } catch (error) {
                console.error('Fetch error:', error);
                resultDiv.innerHTML = `<div class="p-5 text-red-600 font-semibold">
                                            Sorry, we could not get recommendations right now. Please try again later.
                                            <br><small>Error: ${error.message}</small>
                                       </div>`;
            } finally {
                // You can add a visual cue here to indicate loading is done, if not already handled by content replacement
            }
        });
    </script>
    </section>

    <section class="w-full min-h-[40vh]"  data-theme="light" id="services">

      <div class="flex justify-center gap-12 items-center px-6 py-14 max-w-[1000x] mx-auto max-md:flex-col max-md:gap-5 ">
          <div class="lg:text-start text-center grid gap-3"
          data-aos="fade-up"
                 data-aos-duration="2000">
              <h4 class="text-4xl py-3 tracking-tighter font-bold text-[#0118D8]">GET IN TOUCH</h4>
              <p class="text-lg">Feel free to message us with our provided contacts <br> for your inquiries regarding with our dental clinic!</p>
              <a href="#navbar" tooltip="back to top" class="rounded-full p-3 cursor-pointer "><box-icon name='up-arrow-circle' type='solid' size='lg'></box-icon></a>
          </div>
          <div class="flex items-center gap-6 justify-center max-md:flex-col"
          data-aos="fade-up"
                 data-aos-duration="2000">
              <div class="flex items-center gap-4 justify-center flex-col border-2 border-black rounded-lg px-10 max-sm:px-16 py-16">
                  <box-icon type='solid' name='phone-call' class="text-blue text-3xl"></box-icon>
                  <h4 class="text-md">09455373243</h4>
              </div>
              <div class="flex items-center gap-4 justify-center flex-col border-2 border-black rounded-lg px-10 py-16">
                <box-icon type='solid' name='envelope' class="text-blue text-3xl"></box-icon>
                <h4 class="text-sm">kharz891@gmail.com</h4>
            </div>
          </div>
      </div>

    </section>




    @if (session()->has('error'))
    <dialog id="my_modal_24" class="modal" data-theme="light">
      <div class="modal-box">
        <h3 class="text-xl font-bold">Reservation!</h3>
        <p class="py-4 pt-8 text-center text-red-600">{{session('error')}}</p>
        <div class="modal-action">
          <form method="dialog">
            <!-- if there is a button in form, it will close the modal -->
            <button class="btn">Close</button>
          </form>
        </div>
      </div>
      </dialog>

       <!-- JavaScript to automatically open modal -->
    <script>
        // Automatically open modal on page load
        window.addEventListener('DOMContentLoaded', (event) => {
        document.getElementById('my_modal_24').showModal();
        });
    </script>
    @endif

 @if (isset($cancellation) && $cancellation)
        <dialog id="my_modal_100" class="modal">
        <div class="modal-box">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
              </form>
          <h3 class="text-xl text-center font-bold">Cancel Appointment</h3>
          <h5 class="text-md text-blue-600 text-center py-4">Appointment #: {{ $timeslot->appointment_number }}</h5>
           <form action="{{ route('reservations.cancel') }}" class="py-8 pt-4 grid gap-4 items-center" method="POST">
              @csrf
              <input type="hidden" name="appointment_number" value="{{ $timeslot->appointment_number }}">
             <div class="grid">
               <label for="reason">Reason for Cancellation:</label>
              <textarea name="reason" required class="textarea textarea-bordered w-full " placeholder="e.g. I have a conflict at this time."></textarea>
             </div>

              <button type="submit" class="btn btn-error mt-4 ">Cancel Appointment</button>
          </form>
          
        </div>
      </dialog>
      <script>
          document.addEventListener('DOMContentLoaded', function () {
              const dateInput = document.querySelector('input[name="preferred_date"]');

              const today = new Date();
              const maxDate = new Date();
              maxDate.setDate(today.getDate() + 29); // 30-day window including today

              const toDateString = (date) => date.toISOString().split('T')[0];

              dateInput.min = toDateString(today);
              dateInput.max = toDateString(maxDate);
          });
      </script>


       <script>
        // Automatically open modal on page load
        window.addEventListener('DOMContentLoaded', (event) => {
        document.getElementById('my_modal_100').showModal();
        });
     </script>
    @endif

    
    @if (session()->has('success'))
    <dialog id="my_modal_24" class="modal" data-theme="light">
      <div class="modal-box">
        <h3 class="text-xl font-bold">Success!</h3>
        <p class="py-4 pt-8 text-center text-green-600">{{session('success')}}</p>
        <div class="modal-action">
          <form method="dialog">
            <!-- if there is a button in form, it will close the modal -->
            <button class="btn">Close</button>
          </form>
        </div>
      </div>
      </dialog>

       <!-- JavaScript to automatically open modal -->
    <script>
        // Automatically open modal on page load
        window.addEventListener('DOMContentLoaded', (event) => {
        document.getElementById('my_modal_24').showModal();
        });
    </script>
    @endif


</body>
</html>