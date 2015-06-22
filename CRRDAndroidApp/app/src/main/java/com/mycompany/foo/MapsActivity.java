package com.mycompany.foo;

import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.location.Address;
import android.location.Geocoder;
import android.support.v4.app.FragmentActivity;
import android.os.Bundle;

import com.google.android.gms.maps.CameraUpdate;
import com.google.android.gms.maps.CameraUpdateFactory;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.SupportMapFragment;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.Marker;
import com.google.android.gms.maps.model.MarkerOptions;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;


public class MapsActivity extends FragmentActivity {
    private BusinessActivity bs;
    private GoogleMap mMap; // Might be null if Google Play services APK is not available.
    private static final float DEFAULTZOOM = 15;
    String addresssesJSON;
    JSONArray addresses = null;

    // Hashmap for ListView
    ArrayList<HashMap<String, String>> addressesList;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_maps);

        SharedPreferences sharedPref = getSharedPreferences("MyPrefs", Context.MODE_PRIVATE);
        addresssesJSON = sharedPref.getString("addressesJSON", "");

        try {
            setUpMapIfNeeded();
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    @Override
    protected void onResume() {
        super.onResume();
        try {
            setUpMapIfNeeded();
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    /**
     * Sets up the map if it is possible to do so (i.e., the Google Play services APK is correctly
     * installed) and the map has not already been instantiated.. This will ensure that we only ever
     * call {@link #setUpMap()} once when {@link #mMap} is not null.
     * <p/>
     * If it isn't installed {@link SupportMapFragment} (and
     * {@link com.google.android.gms.maps.MapView MapView}) will show a prompt for the user to
     * install/update the Google Play services APK on their device.
     * <p/>
     * A user can return to this FragmentActivity after following the prompt and correctly
     * installing/updating/enabling the Google Play services. Since the FragmentActivity may not
     * have been completely destroyed during this process (it is likely that it would only be
     * stopped or paused), {@link #onCreate(Bundle)} may not be called again so we should call this
     * method in {@link #onResume()} to guarantee that it will be called.
     */
    private void setUpMapIfNeeded() throws IOException {
        // Do a null check to confirm that we have not already instantiated the map.
        if (mMap == null) {
            // Try to obtain the map from the SupportMapFragment.
            mMap = ((SupportMapFragment) getSupportFragmentManager().findFragmentById(R.id.map))
                    .getMap();
            // Check if we were successful in obtaining the map.
            if (mMap != null) {
                setUpMap();
            }
        }
    }

    /* This is the code that will be ran when we are marking a business
        location on the map. the function takes a string as an input,
        which is the address. We can also expand to have the function
        take more inputs so we can add a brief description at the top
        of the marker and display even more when the user clicks on
        the marker.
     */


    public void gotoLocation(double lat,double lng,String name,float zoom){
        LatLng ll = new LatLng(lat,lng);
        Marker marker = mMap.addMarker(new MarkerOptions()
                .position(new LatLng(lat, lng))
                .title(name));
        CameraUpdate update = CameraUpdateFactory.newLatLngZoom(ll,zoom);
        mMap.moveCamera(update);

    }
    public void getLocate(String name,String location) throws IOException {

        Geocoder gc = new Geocoder(this);

        List<Address> list = gc.getFromLocationName(location, 5);
        Address add = list.get(0);
        String locality = add.getLocality();

        double lat = add.getLatitude();
        double lng = add.getLongitude();

        gotoLocation(lat, lng,name,DEFAULTZOOM);
    }

    /**
     * This is where we can add markers or lines, add listeners or move the camera. In this case, we
     * just add a marker near Africa.
     * <p/>
     * This should only be called once and when we are sure that {@link #mMap} is not null.
     */

    public ArrayList<HashMap<String, String>> getAddresses( ){

        addressesList = new ArrayList<HashMap<String, String>>();

        if (addresssesJSON != null) {
            try {

                JSONObject jsonObj = new JSONObject(addresssesJSON);
                // Getting JSON Array node
                addresses = jsonObj.getJSONArray("addresses");

                // looping through All categories
                for (int i = 0; i < addresses.length(); i++) {
                    JSONObject addr = addresses.getJSONObject(i);

                    String address = addr.getString("address");
                    String name = addr.getString("name");

                    // tmp hashmap for single contact
                    HashMap<String, String> addressMap = new HashMap<String, String>();

                    // adding each child node to HashMap key => value
                    addressMap.put("address", address);
                    addressMap.put("name", name);

                    // adding art to artwork list
                    addressesList.add(addressMap);
                }
            } catch (JSONException e) {
                e.printStackTrace();
            }
        }

        return addressesList;
    }


    private void setUpMap() throws IOException {
       // ArrayList<HashMap<String, String>> address ;
      //  String value;
        Intent intent = getIntent();
        String addr = intent.getExtras().getString("street");
        String name = intent.getExtras().getString("company");
        mMap.setMyLocationEnabled(true);
        if(addr.length()>0){
            getLocate(name,addr);
        }else{
            getLocate("Failed","97734");
        }


    }
        //mMap.addMarker(new MarkerOptions().position(new LatLng(44.5646, 123.2757)).title("Marker"));
    }
